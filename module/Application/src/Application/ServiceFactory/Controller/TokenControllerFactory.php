<?php

namespace Application\ServiceFactory\Controller;

use Application\Controller\TokenController;
use Psr\Container\ContainerInterface;
use Auth\Service\TokenService;

class TokenControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); 
        $TokenService = $container->get(TokenService::class);
        return new TokenController($TokenService);
    }
}