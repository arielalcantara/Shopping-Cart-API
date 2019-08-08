<?php
namespace Customer\ServiceFactory\Controller;

use Psr\Container\ContainerInterface;
use Customer\Controller\RegistrationController;
use Customer\Filter\RegistrationFilter;
use Customer\Model\CustomerTable;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Customer\Model\Customer;
use Auth\Service\TokenService;

class RegistrationControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); // remove if zf3
        $registrationFilter = $container->get(RegistrationFilter::class);
        $customerTable = $container->get(CustomerTable::class);
        $cartTable = $container->get(CartTable::class);
        $cartItemTable = $container->get(CartItemTable::class);
        $customer = new Customer;
        $tokenService = $container->get(TokenService::class);

        return new RegistrationController(
            $registrationFilter,
            $customerTable,
            $cartTable,
            $cartItemTable,
            $customer,
            $tokenService
        );
    }
}
