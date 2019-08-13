<?php
namespace Cart\Service;

class CartService
{
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
        $shipping = $this->shippingService->fetchShippings();

        if (!$shipping_method) {
            foreach ($shippings as $shipping) {
                if ($shipping['shipping_method'] == )
            }
        }


        public function calculateShippingTotal($total_weight, $shipping_method)
    {
        $shippings = $this->shippingTable->fetchAllShippingRecords();

        // Perform when total weight is within allowed weight per shipment
        foreach ($shippings as $shipping) {
            if ($shipping['shipping_method'] === $shipping_method) {
                if ($total_weight >= $shipping['min_weight'] && $total_weight <= $shipping['max_weight']) {
                    $runningPrice = $shipping['shipping_rate'];
                    break;
                }
                $maxWeightPerShipment = $shipping['max_weight'];
                $maxRatePerShipment = $shipping['shipping_rate'];
            }
        }

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

        return $runningPrice;
    }
    }
}
