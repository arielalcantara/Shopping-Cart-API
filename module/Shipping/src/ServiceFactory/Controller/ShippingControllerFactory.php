<?php
namespace Shipping\ServiceFactory\Controller;

use Psr\Container\ContainerInterface;
use Shipping\Controller\ShippingController;
use Shipping\Filter\ShippingFilter;
use Shipping\Model\ShippingTable;
use Cart\Model\CartTable;
use Cart\Model\Cart;
use Shipping\Service\ShippingService;

class ShippingControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); // remove if zf3
        $shippingFilter = $container->get(ShippingFilter::class);
        $shippingTable = $container->get(ShippingTable::class);
        $cartTable = $container->get(CartTable::class);
        $cart = new Cart;
        $shippingService = $container->get(ShippingService::class);

        return new ShippingController(
            $shippingFilter,
            $shippingTable,
            $cartTable,
            $cart,
            $shippingService
        );
    }
}
