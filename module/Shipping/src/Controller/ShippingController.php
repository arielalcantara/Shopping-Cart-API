<?php
namespace Shipping\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Shipping\Filter\ShippingFilter;
use Shipping\Model\ShippingTable;
use Cart\Model\CartTable;
use Cart\Model\Cart;
use Shipping\Service\ShippingService;
use Cart\Service\CartService;
use Auth\Service\TokenService;


class ShippingController extends AppAbstractRestfulController
{
    protected $eventIdentifier = 'SecuredController';

    private $shippingFilter;
    private $shippingTable;
    private $cartTable;
    private $cart;
    private $shippingService;
    private $cartService;
    private $tokenService;

    public function __construct(
        ShippingFilter $shippingFilter,
        ShippingTable $shippingTable,
        CartTable $cartTable,
        Cart $cart,
        ShippingService $shippingService,
        CartService $cartService,
        TokenService $tokenService
    ) {
        $this->shippingFilter = $shippingFilter;
        $this->shippingTable = $shippingTable;
        $this->cartTable = $cartTable;
        $this->cart = $cart;
        $this->shippingService = $shippingService;
        $this->cartService = $cartService;
        $this->tokenService = $tokenService;
    }

    public function get($cart_id)
    {
        $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        if ($customer_id != $cartOwner) {
            return $this->createResponse(403, 'Forbidden');
        }

        $cart = $this->cartTable->fetchCartTotalWeight($cart_id);

        $shippingOptions = $this->shippingTable->fetchShippingMethods();

        foreach ($shippingOptions as $shippingOption) {
            $shippingTotals[$shippingOption['shipping_method']] =
                $this->shippingService->calculateShippingTotal(
                    $cart['total_weight'],
                    $shippingOption['shipping_method']
                );
        }

        $data = [
            'success' => true,
            'data' => $shippingTotals
        ];

        return new JsonModel($data);
    }

    public function create($input)
    {
        $inputArray = $this->shippingFilter->validateAndSanitizeInput($input);
        $cart_id = $inputArray['cart_id'];

        $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        if ($customer_id != $cartOwner) {
            return $this->createResponse(403, 'Forbidden');
        }

        if (!$this->shippingFilter->isValid()) {
            return $this->createResponse(400, 'Invalid input.');
        }

        $this->cart->exchangeArray($inputArray);

        $cart = $this->cartTable->fetchCartTotalWeightAndSubTotal($cart_id);

        $this->cart->shipping_total =$this->shippingService->calculateShippingTotal(
            $cart['total_weight'],
            $this->cart->shipping_method
        );

        $this->cart->total_amount = $this->cartService->computeTotalAmount(
            $cart['sub_total'],
            $this->cart->shipping_total
        );

        $this->cartTable->updateCartShippingDetails($cart_id, $this->cart);

        return new JsonModel([
            'success' => true
        ]);
    }
}
