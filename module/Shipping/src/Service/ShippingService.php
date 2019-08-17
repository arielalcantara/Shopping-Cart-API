<?php
namespace Shipping\Service;

use Shipping\Model\ShippingTable;

class ShippingService
{
    private $shippingTable;

    public function __construct(ShippingTable $shippingTable) {
        $this->shippingTable = $shippingTable;
    }

    public function calculateShippingTotals($cart)
    {
        // var_dump($cart->total_weight); exit;
        // fetch shippings
        // arrange shippings by method, store in shippingsArray
        // if !shipping_method
        //      calculate shippingtotals for both methods, store in shippingTotalsArray
        //      return shippingTotalsArray
        // else
        //      calculate shippingtotal for shipping_method
        //      return shippingTotal
        $shippings = $this->shippingTable->fetchShippings($shipping_method);
        $shippingsArray = [];
        $ifExcess = true;

        // foreach ($shippings as $shipping) {
        //     $shippingsArray[$shipping['shipping_method']][] = [
        //         'min_weight' => $shipping['min_weight'],
        //         'max_weight' => $shipping['max_weight'],
        //         'shipping_rate' => $shipping['shipping_rate']
        //     ];

        //     // Perform when total weight is within allowed weight per shipment
        //     if ($cart->total_weight >= $shipping['min_weight'] && $cart->total_weight <= $shipping['max_weight']) {
        //         $shippingTotals[$shipping['shipping_method']] = $shipping['shipping_rate'];
        //         $ifExcess = false;

        //         if ($shipping_method) {
        //             break;
        //         } else {
        //             continue;
        //         }
        //     }

        //     $maxWeightPerShipment[$shipping['shipping_method']] = $shipping['max_weight'];
        //     $maxRatePerShipment[$shipping['shipping_method']] = $shipping['shipping_rate'];
        // }

        // echo '<pre>';
        // var_dump($shippingsArray);
        // echo '</pre>'; exit;

        // Perform when total weight is within allowed weight per shipment
        // foreach ($shippings as $shipping) {
        //     if ($cart->total_weight >= $shipping['min_weight'] && $cart->total_weight <= $shipping['max_weight']) {
        //         $shippingTotals[$shipping['shipping_method']] = $shipping['shipping_rate'];
        //         $ifExcess = false;

        //         if ($shipping_method) {
        //             break;
        //         } else {
        //             continue;
        //         }
        //     }

        //     $maxWeightPerShipment[$shipping['shipping_method']] = $shipping['max_weight'];
        //     $maxRatePerShipment[$shipping['shipping_method']] = $shipping['shipping_rate'];
        // }

        // if ($ifExcess) {
        //     $numOfShipments = floor($cart->total_weight / $maxWeightPerShipment[$shipping['shipping_method']]);
        //     // var_dump($numOfShipments); exit;
        //     $shippingTotals[$shipping['shipping_method']] = $numOfShipments * $maxRatePerShipment;
        //     $remainingWeight = $cart->total_weight % $maxWeightPerShipment;
        //     if ($remainingWeight) {
        //         foreach ($shippings as $shipping) {
        //             if ($remainingWeight >= $shipping['min_weight'] && $remainingWeight <= $shipping['max_weight']) {
        //                 $shippingTotals[$shipping['shipping_method']] += $shipping['shipping_rate'];

        //                 if ($shipping_method) {
        //                     break;
        //                 }
        //             }
        //         }
        //     }
        // }
        // var_dump($shippingTotals); exit;
        // return $shippingTotals;


        // $shippingTotals = array();
        // foreach ($shippings as $shipping) {
        //     if ($cart->total_weight >= $shipping['min_weight'] && $cart->total_weight <= $shipping['max_weight']) {
        //         $shippingTotals[$shipping['shipping_method']] = $shipping['shipping_rate'];
        //         $ifExcess = false;

        //         if ($shipping_method) {
        //             break;
        //         } else {
        //             continue;
        //         }
        //     }

        //     $maxWeightPerShipment[$shipping['shipping_method']] = $shipping['max_weight'];
        //     $maxRatePerShipment[$shipping['shipping_method']] = $shipping['shipping_rate'];
        // }

        // if ($ifExcess) {
        //     $numOfShipments = floor($cart->total_weight / $maxWeightPerShipment[$shipping['shipping_method']]);
        //     // var_dump($numOfShipments); exit;
        //     $shippingTotals[$shipping['shipping_method']] = $numOfShipments * $maxRatePerShipment;
        //     $remainingWeight = $cart->total_weight % $maxWeightPerShipment;
        //     if ($remainingWeight) {
        //         foreach ($shippings as $shipping) {
        //             if ($remainingWeight >= $shipping['min_weight'] && $remainingWeight <= $shipping['max_weight']) {
        //                 $shippingTotals[$shipping['shipping_method']] += $shipping['shipping_rate'];

        //                 if ($shipping_method) {
        //                     break;
        //                 }
        //             }
        //         }
        //     }
        // }
        // var_dump($shippingTotals); exit;
        // return $shippingTotals;


        $availableShippingMethods = $this->getDistinctShippingMethod($shippings);
        $shippingRates = array();

        foreach ($availableShippingMethods as $shippingMethod) {
            $shippingRates[$shippingMethod] = $this->getShippingTotal($shippings, $shippingMethod, $cart->total_weight);
        }

        return $shippingRates;
    }

    private function getDistinctShippingMethod($shippingMethods)
    {
        $distinctShippingMethods = array();

        foreach ($shippingMethods as $shipping) {
            if (in_array($shipping['shipping_method'], $distinctShippingMethods)) {
                $distinctShippingMethods[] = $shipping['shipping_method'];
            }
        }

        return $distinctShippingMethods;
    }

    public function getShippingTotal($shippings, $shipping_method, $weight)
    {
        $maxShippingMethod = $this->getAppropriateShippingMethod($shippings, $shipping_method, $weight);
        $excessWeight = $weight % $maxShippingMethod['max_weight'];

        if ($excessWeight !== 0 && $weight > $maxShippingMethod['max_weight']) {
            $excessShippingMethod = $this->getAppropriateShippingMethod($shippings, $shipping_method, $excessWeight);
        }

        $numOfShipments = floor($weight / $maxShippingMethod['max_weight']);

        return ($numOfShipments * $maxShippingMethod['shipping_rate']) + $excessShippingMethod['shipping_rate'];
    }

    private function getAppropriateShippingMethod($shippingMethods, $shippingMethod, $weight)
    {
        $maxShippingMethod = array();
        foreach ($shippingMethods as $shipping) {
            if ($shipping['max_weight'] && $shippingMethod === $shipping['shipping_method']) {
                $maxShippingMethod = $shipping;

                if ($weight >= $shipping['min_weight'] && $weight) {
                    return $shipping;
                }
            }
        }

        return $maxShippingMethod;
    }

    // public function calculateShippingTotal($total_weight, $shipping_method)
    // {
    //     $shippings = $this->shippingTable->fetchAllShippingRecords();

    //     // Perform when total weight is within allowed weight per shipment
    //     foreach ($shippings as $shipping) {
    //         if ($shipping['shipping_method'] === $shipping_method) {
    //             if ($total_weight >= $shipping['min_weight'] && $total_weight <= $shipping['max_weight']) {
    //                 $runningPrice = $shipping['shipping_rate'];
    //                 break;
    //             }
    //             $maxWeightPerShipment = $shipping['max_weight'];
    //             $maxRatePerShipment = $shipping['shipping_rate'];
    //         }
    //     }

        // Perform when total weight exceeds allowed max weight per shipment
        if (!$runningPrice) {
            $numOfShipments = floor($total_weight / $maxWeightPerShipment);
            $runningPrice = $numOfShipments * $maxRatePerShipment;
            $remainingWeight = $total_weight % $maxWeightPerShipment;

            if ($remainingWeight) {
                foreach ($shippings as $shipping) {
                    if ($shipping['shipping_method'] === $shipping_method) {
                        if ($remainingWeight >= $shipping['min_weight'] && $remainingWeight <= $shipping['max_weight']) {
                            $runningPrice += $shipping['shipping_rate'];
                            break;
                        }
                    }
                }
            }
        }

    //     return $runningPrice;
    // }
}
