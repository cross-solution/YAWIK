<?php

return [

    'options' => [
        'Geo/Options' => [
            'class' => '\Geo\Options\ModuleOptions',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Geo\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => [ __DIR__ . '/../src/Geo/Entity'],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            'Geo/Client' => 'Geo\Factory\Service\ClientFactory',
            \Geo\Listener\AjaxQuery::class => \Geo\Factory\Listener\AjaxQueryFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            'Geo\Controller\Index' => 'Geo\Factory\Controller\IndexControllerFactory',
        ]
    ],
    
    // Routes
    'router' => [
        'routes' => [
            'lang' => [
                'child_routes' => [
                    'geo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/geo[/:plugin]',
                            'defaults' => [
                                'controller' => 'Geo\Controller\Index',
                                'action' => 'index',
                                'module' => 'Geo',
                                'plugin' => 'photon'
                            ],
                            'constraints' => [
                                'plugin' => '(geo|photon)',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'SimpleLocationSelect' => 'Geo\Form\GeoSelectSimple',
        ],
        'factories' => [
            'LocationSelect' => 'Geo\Factory\Form\GeoSelectFactory',
        ],
    ],
    
    'event_manager' => [
        'Core/Ajax/Events' => ['listeners' => [
            \Geo\Listener\AjaxQuery::class => ['geo', true],
        ]]
    ],

];
