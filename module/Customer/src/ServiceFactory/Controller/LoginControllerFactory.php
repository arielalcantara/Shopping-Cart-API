<?php
namespace Customer\ServiceFactory\Controller;

use Psr\Container\ContainerInterface;
use Customer\Controller\LoginController;
use Customer\Filter\LoginFilter;
use Customer\Model\CustomerTable;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Customer\Model\Customer;
use Auth\Service\TokenService;

class LoginControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); // remove if zf3
        $loginFilter = $container->get(LoginFilter::class);
        $customerTable = $container->get(CustomerTable::class);
        $cartTable = $container->get(CartTable::class);
        $cartItemTable = $container->get(CartItemTable::class);
        $customer = new Customer;
        $tokenService = $container->get(TokenService::class);

        return new LoginController(
            $loginFilter,
            $customerTable,
            $cartTable,
            $cartItemTable,
            $customer,
            $tokenService
        );
    }
}
