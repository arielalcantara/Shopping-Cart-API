<?php
namespace Job\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Model\JobOrder;
use Job\Model\JobItemTable;
use Job\Model\JobOrderTable;
use Shipping\Service\ShippingService;
use Cart\Service\CartService;
use Auth\Service\TokenService;

class JobController extends AppAbstractRestfulController
{
    protected $eventIdentifier = 'SecuredController';

    private $cartItemTable;
    private $cartTable;
    private $jobOrder;
    private $jobItemTable;
    private $jobOrderTable;
    private $shippingService;
    private $cartService;
    private $tokenService;

    public function __construct(
        CartItemTable $cartItemTable,
        CartTable $cartTable,
        JobOrder $jobOrder,
        JobItemTable $jobItemTable,
        JobOrderTable $jobOrderTable,
        ShippingService $shippingService,
        CartService $cartService,
        TokenService $tokenService

    ) {
        $this->cartItemTable = $cartItemTable;
        $this->cartTable = $cartTable;
        $this->jobOrder = $jobOrder;
        $this->jobItemTable = $jobItemTable;
        $this->jobOrderTable = $jobOrderTable;
        $this->shippingService = $shippingService;
        $this->cartService = $cartService;
        $this->tokenService = $tokenService;
    }

    public function create($input)
    {
        $cart_id = $input['cart_id'];

        if (!$cart_id) {
            return $this->createResponse(400, 'Invalid payment.');
        }

        $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        if ($customer_id != $cartOwner) {
            return $this->createResponse(403, 'Forbidden');
        }

        $cart = $this->cartTable->fetchCartTotalsAndShippingMethod($cart_id);

        if (!$cart->shipping_method) {
            return $this->createResponse(403, 'Shipping details not found. Please proceed to shipping page.');
        }

        $cart->shipping_total = $this->shippingService->calculateShippingTotal(
            $cart->total_weight,
            $cart->shipping_method
        );

        $cart->total_amount = $this->cartService->computeTotalAmount(
            $cart->sub_total,
            $cart->shipping_total
        );

        $this->cartTable->updateCartTotals($cart_id, $cart);

        $this->jobOrder = $this->cartTable->fetchCart($cart_id);
        $job_order_id = $this->jobOrderTable->insertJobOrder($this->jobOrder);

        $jobItems = $this->cartItemTable->fetchCartItemsByCart($cart_id);

        foreach ($jobItems as $jobItem) {
            $jobItem['job_order_id'] = $job_order_id;
            $this->jobItemTable->insertJobItem($jobItem);
        }

        $this->cartItemTable->deleteCartItemByCart($cart_id);
        $this->cartTable->deleteCart($cart_id);

        return new JsonModel([
            'success'      => true,
            'data' => $job_order_id
        ]);
    }

    public function get($job_order_id)
    {
        $jobOwner = $this->jobOrderTable->getCustomerIdByJob($job_order_id);

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $customer_id = $this->tokenService->getCustomerIdInAccessToken($authHeader);

        if ($customer_id != $jobOwner) {
            return $this->createResponse(403, 'Forbidden');
        }

        $jobItems = $this->jobItemTable->fetchAllJobItemsByJobOrder($job_order_id);
        $jobOrder = $this->jobOrderTable->fetchJobOrder($job_order_id);

        $data = ([
            'jobItems'   => $jobItems,
            'jobOrder'   => $jobOrder,
            'jobOrderId' => $job_order_id
        ]);

        return new JsonModel([
            'success' => true,
            'data'    => $data
        ]);
    }
}
