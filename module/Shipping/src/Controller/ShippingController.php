<?php
namespace Shipping\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Shipping\Filter\ShippingFilter;
use Shipping\Model\ShippingTable;
use Cart\Model\CartTable;
use Cart\Model\Cart;
use Cart\Service\CartService;
use Shipping\Service\ShippingService;

class ShippingController extends AppAbstractRestfulController
{
    protected $eventIdentifier = 'SecuredController';

    private $shippingFilter;
    private $shippingTable;
    private $cartTable;
    private $cart;
    private $cartService;
    private $shippingService;

    public function __construct(
        ShippingFilter $shippingFilter,
        ShippingTable $shippingTable,
        CartTable $cartTable,
        Cart $cart,
        CartService $cartService,
        ShippingService $shippingService
    ) {
        $this->shippingFilter = $shippingFilter;
        $this->shippingTable = $shippingTable;
        $this->cartTable = $cartTable;
        $this->cart = $cart;
        $this->cartService = $cartService;
        $this->shippingService = $shippingService;
    }

    public function get($cart_id)
    {
        $customer_id = $this->getCustomerIdFromHeader();

        $cart = $this->cartTable->getCart($cart_id, $customer_id);

        if (!$cart) {
            return $this->createResponse(403, 'Forbidden');
        }

        $shippingTotals = $this->shippingService->getShippingTotals($cart, $cart->shipping_method);

        $data = [
            'success' => true,
            'data' => $shippingTotals
        ];

        return new JsonModel($data);
    }

    public function create($input)
    {
        $inputArray = $this->shippingFilter->validateAndSanitizeInput($input);

        if (!$this->shippingFilter->isValid()) {
            return $this->createResponse(400, 'Invalid input.');
        }

        $customer_id = $this->getCustomerIdFromHeader();

        $cart = $this->cartTable->getCart($inputArray['cart_id'], $customer_id);

        if (!$cart) {
            return $this->createResponse(403, 'Forbidden');
        }

        $this->cartService->updateCartShippingDetails($cart, $inputArray);

        return new JsonModel([
            'success' => true
        ]);
    }
}
