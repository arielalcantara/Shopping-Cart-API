<?php

namespace Auth\ServiceFactory\Service;

use Auth\Service\TokenService;
use Psr\Container\ContainerInterface;

class TokenServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new TokenService();
    }
}
