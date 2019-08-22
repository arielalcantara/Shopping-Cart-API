<?php
namespace Cart\Service;

use Shipping\Service\ShippingService;

class CartService
{
    private $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
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

    public function updateCartShippingDetails($cart, $shippingInput)
    {
        $cartSubTotal = $cart->sub_total;
        $cartTotalWeight = $cart->total_weight;
        $cart->exchangeArray($shippingInput);

        $cart->shipping_total = $this->shippingService->getShippingTotal(
            $cart->shipping_method,
            $cartTotalWeight
        );

        $cart->total_amount = $this->computeTotalAmount(
            $cartSubTotal,
            $cart->shipping_total
        );

        return $this->cartTable->updateCartShippingDetails($cart->cart_id, $cart);
    }

    public function computeTotalAmount($sub_total, $shipping_total)
    {
        $total_amount = $sub_total + $shipping_total;

        return $total_amount;
    }
}
