<?php
namespace Cart\Service;

use Shipping\Model\ShippingTable;

class CartService
{
    public function __construct(ShippingTable $shippingTable) {
        $this->shippingTable = $shippingTable;
    }

    public function computeTotals($cartItemArray, $cart)
    {
        if ($cart->total_amount) {
            $cart->sub_total = $cart->sub_total + $cartItemArray['price'];
            $cart->total_amount = $cart->total_amount + $cart->shipping_total + $cartItemArray['price'];
            $cart->total_weight = $cart->total_weight + $cartItemArray['weight'];
        } else {
            $cart->sub_total = $cartItemArray['price'];
            $cart->total_amount = $cartItemArray['price'];
            $cart->total_weight = $cartItemArray['weight'];
        }

        return $cart;
    }

    public function computeTotalAmount($sub_total, $shipping_total)
    {
        $total_amount = $sub_total + $shipping_total;

        return $total_amount;
    }

    public function calculateShippingTotals($cart, $shipping_method = '')
    {
        $shippings = $this->shippingTable->fetchShippings();
        $shippingsArray = [];

        if (!$shipping_method) {
            foreach ($shippings as $shipping) {
                $shippingsArray[$shipping['shipping_method']][] = [
                        'min_weight' => $shipping['min_weight'],
                        'max_weight' => $shipping['max_weight'],
                        'shipping_rate' => $shipping['shipping_rate']
                ];
                var_dump($shippingsArray);

                // if ($shipping['shipping_method'] !== $shippingsArray['shipping_method']) {
                //     $shippingsArray['shipping_method'] = $shipping['shipping_method'];
                // }
                // $shippingsArray[][i] = [
                //     'min_weight' => $shipping['min_weight'],
                //     'max_weight' => $shipping['max_weight'],
                //     'shipping_rate' => $shipping['shipping_rate']
                // ];
                // i++;
            }
        }
    }

    public function calculateShippingTotal($total_weight, $shipping_method = '')
    {
        $shippings = $this->shippingTable->fetchAllShippingRecords($shipping_method);

        $shippingTotals = [];
        $ifExcess = false;
        // Perform when total weight is within allowed weight per shipment
        foreach ($shippings as $shipping) {
            if ($total_weight >= $shipping['min_weight'] && $total_weight <= $shipping['max_weight']) {
                $shippingTotals[$shipping['shipping_method']] = $shipping['shipping_rate'];
                break;
            } else {
                $ifExcess = true;
            }
            $maxWeightPerShipment = $shipping['max_weight'];
            $maxRatePerShipment = $shipping['shipping_rate'];
        }

        // Perform when total weight exceeds allowed max weight per shipment
        if ($ifExcess) {
            $numOfShipments = floor($total_weight / $maxWeightPerShipment);
            $shippingTotals[$shipping['shipping_method']] = $numOfShipments * $maxRatePerShipment;
            $remainingWeight = $total_weight % $maxWeightPerShipment;

            if ($remainingWeight) {
                foreach ($shippings as $shipping) {
                    if ($remainingWeight >= $shipping['min_weight'] && $remainingWeight <= $shipping['max_weight']) {
                        $shippingTotals[$shipping['shipping_method']] += $shipping['shipping_rate'];
                        break;
                    }
                }
            }
        }

        return $shippingTotals[$shipping['shipping_method']];
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

    //     // Perform when total weight exceeds allowed max weight per shipment
    //     if (!$runningPrice) {
    //         $numOfShipments = floor($total_weight / $maxWeightPerShipment);
    //         $runningPrice = $numOfShipments * $maxRatePerShipment;
    //         $remainingWeight = $total_weight % $maxWeightPerShipment;

    //         if ($remainingWeight) {
    //             foreach ($shippings as $shipping) {
    //                 if ($shipping['shipping_method'] === $shipping_method) {
    //                     if ($remainingWeight >= $shipping['min_weight'] && $remainingWeight <= $shipping['max_weight']) {
    //                         $runningPrice += $shipping['shipping_rate'];
    //                         break;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return $runningPrice;
    // }

}
