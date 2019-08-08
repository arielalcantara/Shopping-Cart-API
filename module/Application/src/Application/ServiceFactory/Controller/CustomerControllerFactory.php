<?php

namespace Application\ServiceFactory\Controller;

use Application\Controller\CustomerController;
use Psr\Container\ContainerInterface;

class CustomerControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator();   
        return new CustomerController();
    }
}