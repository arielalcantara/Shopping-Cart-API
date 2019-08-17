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
}
