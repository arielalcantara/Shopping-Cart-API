<?php
namespace Cart;

use Zend\Mvc\Router\Http\Segment;
use Cart\Controller\CartController;
use Cart\ServiceFactory\Controller\CartControllerFactory;
use Cart\Model\CartTable;
use Cart\ServiceFactory\Model\CartTableFactory;
use Cart\Model\CartItemTable;
use Cart\ServiceFactory\Model\CartItemTableFactory;
use Cart\Filter\CartFilter;
use Cart\Service\CartService;
use Cart\ServiceFactory\Service\CartServiceFactory;
use Cart\Service\CartItemService;
use Cart\ServiceFactory\Service\CartItemServiceFactory;

return [
    'router' => [
        'routes' => [
            'cart' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cart[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => CartController::class,
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            CartController::class => CartControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            CartTable::class => CartTableFactory::class,
            CartItemTable::class => CartItemTableFactory::class,

            CartService::class => CartServiceFactory::class,
            CartItemService::class => CartItemServiceFactory::class,
        ],
        'invokables' => [
            CartFilter::class => CartFilter::class
        ]
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
