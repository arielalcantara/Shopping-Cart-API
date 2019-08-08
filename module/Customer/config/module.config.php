<?php
namespace Customer;

use Zend\Mvc\Router\Http\Segment;
use Customer\Controller\LoginController;
use Customer\ServiceFactory\Controller\LoginControllerFactory;
use Customer\Controller\RegistrationController;
use Customer\ServiceFactory\Controller\RegistrationControllerFactory;
use Customer\Model\CustomerTable;
use Customer\ServiceFactory\Model\CustomerTableFactory;
use Customer\Filter\LoginFilter;
use Customer\Filter\RegistrationFilter;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/customer/login',
                    'constraints' => [
                        'from' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => LoginController::class
                    ],
                ],
            ],
            'registration' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/customer/registration',
                    'constraints' => [
                        'from' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => RegistrationController::class
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            LoginController::class => LoginControllerFactory::class,
            RegistrationController::class => RegistrationControllerFactory::class
        ],
    ],
    'service_manager' => [
        'factories' => [
            CustomerTable::class => CustomerTableFactory::class
        ],
        'invokables' => [
            LoginFilter::class => LoginFilter::class,
            RegistrationFilter::class => RegistrationFilter::class
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
