<?php
namespace Shipping\ServiceFactory\Service;

use Psr\Container\ContainerInterface;
use Shipping\Service\ShippingService;
use Shipping\Model\ShippingTable;

class ShippingServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $shippingTable = $container->get(ShippingTable::class);

        return new ShippingService($shippingTable);
    }
}
