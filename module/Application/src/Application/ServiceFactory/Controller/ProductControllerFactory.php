<?php

namespace Application\ServiceFactory\Controller;

use Application\Controller\ProductController;
use Psr\Container\ContainerInterface;

class ProductControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator();   
        return new ProductController();
    }
}