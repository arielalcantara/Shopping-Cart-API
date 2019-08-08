<?php
namespace Cart\Service;

class CartItemService
{
    public function computeCartItemSum($cartItem, $oldCartItemArray)
    {
        $cartItem->weight = $cartItem->weight + $oldCartItemArray['weight'];
        $cartItem->qty = $cartItem->qty + $oldCartItemArray['qty'];
        $cartItem->price = $cartItem->price + $oldCartItemArray['price'];

        return $cartItem;
    }
}
