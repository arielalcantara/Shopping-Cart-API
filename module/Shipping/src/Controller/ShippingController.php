<?php
namespace Shipping\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Shipping\Filter\ShippingFilter;
use Shipping\Model\ShippingTable;
use Cart\Model\CartTable;
use Cart\Model\Cart;
use Cart\Service\CartService;
use Zend\View\Helper\Json;

class ShippingController extends AppAbstractRestfulController
{
    // protected $eventIdentifier = 'SecuredController';

    private $shippingFilter;
    private $shippingTable;
    private $cartTable;
    private $cart;
    private $cartService;

    public function __construct(
        ShippingFilter $shippingFilter,
        ShippingTable $shippingTable,
        CartTable $cartTable,
        Cart $cart,
        CartService $cartService
    ) {
        $this->shippingFilter = $shippingFilter;
        $this->shippingTable = $shippingTable;
        $this->cartTable = $cartTable;
        $this->cart = $cart;
        $this->cartService = $cartService;
    }

    public function get($cart_id)
    {
        $customer_id = $this->getCustomerIdFromHeader();

        $cart = $this->cartTable->getCart($cart_id, $customer_id = 0);

        $this->cartService->calculateShippingTotals($cart);
        exit;


        // if ($cart) {
        //     return $this->createResponse(403, 'Forbidden');
        // }

        // foreach ($shippingOptions as $shippingOption) {
        //     $shippingTotals[$shippingOption['shipping_method']] =
        //         $this->shippingService->calculateShippingTotal(
        //             $cart['total_weight'],
        //             $shippingOption['shipping_method']
        //         );
        // }

        // $data = [
        //     'success' => true,
        //     'data' => $shippingTotals
        // ];

        // return new JsonModel($data);


        // // Start of previous code
        // $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

        // $customer_id = $this->getCustomerIdFromHeader();

        // if ($customer_id != $cartOwner) {
        //     return $this->createResponse(403, 'Forbidden');
        // }

        // $cart = $this->cartTable->fetchCartTotalWeight($cart_id);
        // // Refactor
        // $shippingOptions = $this->shippingTable->fetchShippingMethods(); // remove, fetch only inside shippingService

        // foreach ($shippingOptions as $shippingOption) {
        //     $shippingTotals[$shippingOption['shipping_method']] =
        //         $this->shippingService->calculateShippingTotal(
        //             $cart['total_weight'],
        //             $shippingOption['shipping_method']
        //         );
        // }

        // $data = [
        //     'success' => true,
        //     'data' => $shippingTotals
        // ];

        // return new JsonModel($data);
    }

    // public function create($input)
    // {
    //     $inputArray = $this->shippingFilter->validateAndSanitizeInput($input);
    //     $cart_id = $inputArray['cart_id'];

    //     $cartOwner = $this->cartTable->getCustomerIdByCart($cart_id);

    //     $customer_id = $this->getCustomerIdFromHeader();

    //     if ($customer_id != $cartOwner) {
    //         return $this->createResponse(403, 'Forbidden');
    //     }

    //     if (!$this->shippingFilter->isValid()) {
    //         return $this->createResponse(400, 'Invalid input.');
    //     }

    //     $this->cart->exchangeArray($inputArray);

    //     $cart = $this->cartTable->fetchCartTotalWeightAndSubTotal($cart_id);

    //     $this->cart->shipping_total =$this->shippingService->calculateShippingTotal(
    //         $cart['total_weight'],
    //         $this->cart->shipping_method
    //     );

    //     $this->cart->total_amount = $this->cartService->computeTotalAmount(
    //         $cart['sub_total'],
    //         $this->cart->shipping_total
    //     );

    //     $this->cartTable->updateCartShippingDetails($cart_id, $this->cart);

    //     return new JsonModel([
    //         'success' => true
    //     ]);
    // }
}
