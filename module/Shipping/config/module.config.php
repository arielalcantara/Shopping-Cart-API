<?php
namespace Shipping;

use Zend\Mvc\Router\Http\Segment;
use Shipping\Controller\ShippingController;
use Shipping\ServiceFactory\Controller\ShippingControllerFactory;
use Shipping\Model\ShippingTable;
use Shipping\ServiceFactory\Model\ShippingTableFactory;
use Shipping\Filter\ShippingFilter;
use Shipping\Service\ShippingService;
use Shipping\ServiceFactory\Service\ShippingServiceFactory;

return [
    'router' => [
        'routes' => [
            'shipping' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/shipping[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => ShippingController::class
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            ShippingController::class => ShippingControllerFactory::class
        ],
    ],
    'service_manager' => [
        'factories' => [
            ShippingTable::class => ShippingTableFactory::class,
            ShippingService::class => ShippingServiceFactory::class
        ],
        'invokables' => [
            ShippingFilter::class => ShippingFilter::class
        ]
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],
];
