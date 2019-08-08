<?php
namespace Cart\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Product\Model\ProductTable;
use Product\Service\ProductService;
use Cart\Model\CartTable;
use Cart\Filter\CartFilter;
use Cart\Service\CartService;
use Customer\Model\CustomerTable;
use Cart\Model\CartItemTable;
use Cart\Model\Cart;
use Cart\Model\CartItem;
use Cart\Service\CartItemService;
use Shipping\Service\ShippingService;
use Auth\Service\TokenService;

class CartController extends AppAbstractRestfulController
{
    private $cartFilter;
    private $productTable;
    private $cartTable;
    private $cartItemTable;
    private $cartService;
    private $cartItemService;
    private $productService;
    private $shippingService;
    private $customerTable;
    private $cart;
    private $cartItem;
    private $tokenService;

    public function __construct(
        CartFilter $cartFilter,
        ProductTable $productTable,
        CartTable $cartTable,
        CartItemTable $cartItemTable,
        CartService $cartService,
        CartItemService $cartItemService,
        ProductService $productService,
        ShippingService $shippingService,
        CustomerTable $customerTable,
        Cart $cart,
        CartItem $cartItem,
        TokenService $tokenService
    ) {
        $this->cartFilter = $cartFilter;
        $this->productTable = $productTable;
        $this->cartTable = $cartTable;
        $this->cartItemTable = $cartItemTable;
        $this->cartService = $cartService;
        $this->cartItemService = $cartItemService;
        $this->productService = $productService;
        $this->shippingService = $shippingService;
        $this->customerTable = $customerTable;
        $this->cart = $cart;
        $this->cartItem = $cartItem;
        $this->tokenService = $tokenService;
    }

    public function create($input)
    {
        $inputArray = $this->cartFilter->validateAndSanitizeInput($input);

        if (!$this->cartFilter->isValid()) {
            return $this->createResponse(400, 'Invalid input.');
        }

        $product_id = $inputArray['product_id'];
        $qty = $inputArray['qty'];
        $cart_id = $inputArray['cart_id'];

        $productArray = $this->productTable->fetchProductInfo($product_id);

        if (!$productArray) {
            return $this->createResponse(404, 'Product does not exist.');
        }

        if (!$productArray['stock_qty']) {
            return $this->createResponse(200, 'Product is currently out of stock.');
        }

        if ($inputArray['qty'] > $productArray['stock_qty']) {
            return $this->createResponse(200, 'Quantity must not exceed available stock.');
        }

        $cartItemArray = $this->productService->computeProductWeightAndPrice($productArray, $qty);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        // Cart
        if ($cart_id) {
            $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

            if ($customer_id != $cartOwner) {
                return $this->createResponse(403, 'Forbidden');
            }

            $cart = $this->cartTable->fetchCartTotals($cart_id);
            $cart = $this->cartService->computeTotals($cartItemArray, $cart);

            if ($cart->shipping_total > 0) {
                $cart->shipping_method = $this->cartTable->fetchCartShippingMethod($cart_id);
                $cart->shipping_total = $this->shippingService->calculateShippingTotal(
                    $cart->total_weight,
                    $cart->shipping_method
                );
            }

            $this->cartTable->updateCart($cart_id, $cart);
        } else {
            if ($customer_id) {
                $customerArray = $this->customerTable->fetchCustomerInfo($customer_id);

                $this->cart->exchangeArray($customerArray);
                $this->cart->customer_id = $customer_id;
            } else {
                $cart->customer_id = $customer_id;
            }

            $cart = $this->cartService->computeTotals($cartItemArray, $this->cart);

            $cart_id = $this->cartTable->insertCart($cart);
        }

        // Cart Item
        $this->cartItem->exchangeArray($cartItemArray);
        $this->cartItem->cart_id = $cart_id;
        $this->cartItem->product_id = $product_id;
        $this->cartItem->qty = $qty;
        $this->cartItem->unit_price = $productArray['price'];

        $oldCartItemArray = $this->cartItemTable->fetchCartItemByCartAndProduct($cart_id, $product_id);

        if (!$oldCartItemArray) {
            $this->cartItemTable->insertCartItem($this->cartItem);
        } else {
            $this->cartItem = $this->cartItemService->computeCartItemSum($this->cartItem, $oldCartItemArray);

            $this->cartItem->cart_item_id = $oldCartItemArray['cart_item_id'];

            $this->cartItemTable->updateCartItem($this->cartItem->cart_item_id, $this->cartItem);
        }

        return new JsonModel([
            'success' => true,
            'cart_id' => $cart_id
        ]);
    }

    public function get($cart_id)
    {
        $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        if ($customer_id != $cartOwner) {
            return $this->createResponse(403, 'Forbidden');
        }

        $cartItems = $this->cartItemTable->fetchAllCartItems($cart_id);

        if (!$cartItems) {
            return $this->createResponse(404, 'Cart does not exist.');
        }

        $cartTotals = $this->cartTable->fetchCartTotals($cart_id);

        $data = [
            'cartItems'  => $cartItems,
            'cartTotals' => $cartTotals
        ];

        return new JsonModel([
            'success' => true,
            'data'    => $data
        ]);
    }
}
