<?php
namespace Cart\ServiceFactory\Service;

use Psr\Container\ContainerInterface;
use Cart\Service\CartService;
use Shipping\Service\ShippingService;

class CartServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $shippingService = $container->get(ShippingService::class);

        return new CartService($shippingService);
    }
}
