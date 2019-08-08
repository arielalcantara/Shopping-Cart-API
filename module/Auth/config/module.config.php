<?php
namespace Auth;

use Auth\Service\TokenService;
use Auth\ServiceFactory\Service\TokenServiceFactory;
use Zend\Mvc\Router\Http\Segment;

return array(
    'service_manager' => array(
        'factories' => array(
            TokenService::class => TokenServiceFactory::class,
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
