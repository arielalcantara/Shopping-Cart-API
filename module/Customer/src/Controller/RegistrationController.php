<?php
namespace Customer\Controller;

use Application\Controller\AppAbstractRestfulController;
use Psr\Container\ContainerInterface;
use Zend\View\Model\JsonModel;
use Customer\Filter\RegistrationFilter;
use Customer\Model\CustomerTable;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Customer\Model\Customer;
use Auth\Service\TokenService;

class RegistrationController extends AppAbstractRestfulController
{
    private $registrationFilter;
    private $customerTable;
    private $cartTable;
    private $cartItemTable;
    private $customer;
    private $tokenService;

    public function __construct(
        RegistrationFilter $registrationFilter,
        CustomerTable $customerTable,
        CartTable $cartTable,
        CartItemTable $cartItemTable,
        Customer $customer,
        TokenService $tokenService
    ) {
        $this->registrationFilter = $registrationFilter;
        $this->customerTable = $customerTable;
        $this->cartTable = $cartTable;
        $this->cartItemTable = $cartItemTable;
        $this->customer = $customer;
        $this->tokenService = $tokenService;
    }

    public function create($input) {
        $inputArray = $this->registrationFilter->validateAndSanitizeInput($input);

        if (!$this->registrationFilter->isValid()) {
            return $this->createResponse(400, 'Invalid input.');
        }

        if ($inputArray['password'] != $inputArray['confirm_password']) {
            return $this->createResponse(400, 'Passwords given do not match.');
        }

        $emailExists = $this->customerTable->checkIfEmailExists($inputArray['email']);

        if ($emailExists) {
            return $this->createResponse(400, 'E-mail address is already taken.');
        }

        $this->customer->exchangeArray($inputArray);
        $this->customerTable->insertCustomer($this->customer);

        $customerArray = $this->customerTable->fetchCustomerIdAndFirstNameByEmail($this->customer->email);

        if ($inputArray['cart_id']) {
            // Validate that cart_id is customer's
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
