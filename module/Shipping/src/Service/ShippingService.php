<?php
namespace Shipping\Service;

use Shipping\Model\ShippingTable;

class ShippingService
{
    private $shippingTable;

    public function __construct(ShippingTable $shippingTable) {
        $this->shippingTable = $shippingTable;
    }

    public function getShippingTotals($cart)
    {
        $shippings = $this->shippingTable->fetchShippings();
        $availableShippingMethods = $this->getDistinctShippingMethod($shippings);
        $shippingRates = [];

        foreach ($availableShippingMethods as $shippingMethod) {
            $shippingRates[$shippingMethod] = $this->getShippingTotal($shippingMethod, $cart->total_weight, $shippings);
        }

        return $shippingRates;
    }

    public function getShippingTotal($shipping_method, $weight, $shippings = [])
    {
        if (!$shippings) {
            $shippings = $this->shippingTable->fetchShippings($shipping_method);
        }

        $maxShipping = $this->getAppropriateShippingMethod($shipping_method, $weight, $shippings);
        $excessWeight = $weight % $maxShipping['max_weight'];

        if ($excessWeight !== 0 && $weight > $maxShipping['max_weight']) {
            $excessShipping = $this->getAppropriateShippingMethod($shipping_method, $excessWeight, $shippings);
            $numOfShipments = floor($weight / $maxShipping['max_weight']);
        } else {
            $numOfShipments = floor($maxShipping['max_weight'] / $weight);
        }

        return ($numOfShipments * $maxShipping['shipping_rate']) + $excessShipping['shipping_rate'];
    }

    private function getDistinctShippingMethod($shippings)
    {
        $distinctShippings = [];

        foreach ($shippings as $shipping) {
            if (!in_array($shipping['shipping_method'], $distinctShippings)) {
                $distinctShippings[] = $shipping['shipping_method'];
            }
        }

        return $distinctShippings;
    }

    private function getAppropriateShippingMethod($shipping_method, $weight, $shippings)
    {
        $maxShipping = [];

        foreach ($shippings as $shipping) {
            if ($shipping_method === $shipping['shipping_method']) {
                $maxShipping = $shipping;

                if ($weight >= $shipping['min_weight'] && $weight <= $shipping['max_weight']) {
                    return $shipping;
                }
            }
        }

        return $maxShipping;
    }
}
