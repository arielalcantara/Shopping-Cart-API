<?php
namespace Customer\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Customer\Filter\LoginFilter;
use Customer\Model\CustomerTable;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Customer\Model\Customer;
use Auth\Service\TokenService;

class LoginController extends AppAbstractRestfulController
{
    private $loginFilter;
    private $customerTable;
    private $cartTable;
    private $cartItemTable;
    private $customer;
    private $tokenService;

    public function __construct(
        LoginFilter $loginFilter,
        CustomerTable $customerTable,
        CartTable $cartTable,
        CartItemTable $cartItemTable,
        Customer $customer,
        TokenService $tokenService
    ) {
        $this->loginFilter = $loginFilter;
        $this->customerTable = $customerTable;
        $this->cartTable = $cartTable;
        $this->cartItemTable = $cartItemTable;
        $this->customer = $customer;
        $this->tokenService = $tokenService;
    }

    public function create($input) {
        $inputArray = $this->loginFilter->validateAndSanitizeInput($input);

        if (!$this->loginFilter->isValid()) {
            return $this->createResponse(400, 'Invalid input.');
        }

        $customerArray = $this->customerTable->fetchCustomerInfoByEmail($inputArray['email']);

        if (!$customerArray) {
            return $this->createResponse(400, 'Account does not exist.');
        }

        if ($customerArray['password'] != $inputArray['password']) {
            return $this->createResponse(401, 'Incorrect password.');
        }

        if ($inputArray['cart_id']) {
            $this->cartTable->updateCartCustomerIdByCart($customerArray['customer_id'], $inputArray['cart_id']);
        }

        $token = $this->tokenService->generateToken([
            'customer_id' => $customerArray['customer_id'],
            'first_name' => $customerArray['first_name']
        ]);

        return new JsonModel([
            'success' => true,
            'token' => $token
        ]);
    }
}
