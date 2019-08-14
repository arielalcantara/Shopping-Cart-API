<?php
namespace Cart\ServiceFactory\Service;

use Psr\Container\ContainerInterface;
use Cart\Service\CartService;
use Shipping\Model\ShippingTable;

class CartServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $shippingTable = $container->get(ShippingTable::class);

        return new CartService($shippingTable);
    }
}
