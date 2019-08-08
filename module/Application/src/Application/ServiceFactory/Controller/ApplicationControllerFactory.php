<?php

namespace Application\ServiceFactory\Controller;

use Application\Controller\ApplicationController;
use Psr\Container\ContainerInterface;

class ApplicationControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator();   
        return new ApplicationController();
    }
}