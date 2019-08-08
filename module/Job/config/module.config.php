<?php
namespace Job;

use Zend\Mvc\Router\Http\Segment;
use Job\Controller\JobController;
use Job\ServiceFactory\Controller\JobControllerFactory;
use Job\Model\JobOrderTable;
use Job\ServiceFactory\Model\JobOrderTableFactory;
use Job\Model\JobItemTable;
use Job\ServiceFactory\Model\JobItemTableFactory;

return [
    'router' => [
        'routes' => [
            'job' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/job[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => JobController::class
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            JobController::class => JobControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            JobOrderTable::class => JobOrderTableFactory::class,
            JobItemTable::class => JobItemTableFactory::class
        ],
        'invokables' => [
        ]
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
