<?php
namespace Job\ServiceFactory\Controller;

use Psr\Container\ContainerInterface;
use Job\Controller\JobController;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Model\JobOrder;
use Job\Model\JobItemTable;
use Job\Model\JobOrderTable;
use Shipping\Service\ShippingService;
use Cart\Service\CartService;
use Auth\Service\TokenService;

class JobControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); // remove if zf3
        $cartItemTable = $container->get(CartItemTable::class);
        $cartTable = $container->get(CartTable::class);
        $jobOrder = new JobOrder;
        $jobItemTable = $container->get(JobItemTable::class);
        $jobOrderTable = $container->get(JobOrderTable::class);
        $shippingService = $container->get(ShippingService::class);
        $cartService = $container->get(CartService::class);
        $tokenService = $container->get(TokenService::class);

        return new JobController(
            $cartItemTable,
            $cartTable,
            $jobOrder,
            $jobItemTable,
            $jobOrderTable,
            $shippingService,
            $cartService,
            $tokenService
        );
    }
}
