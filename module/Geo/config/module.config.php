<?php

return [

    'options' => [
        'Geo/Options' => [
            'class' => '\Geo\Options\ModuleOptions',
        ],
    ],

    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Geo\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => array( __DIR__ . '/../src/Geo/Entity'),
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    '\Jobs\Repository\Event\UpdatePermissionsSubscriber',
                ),
            ),
        ),
    ),

    'service_manager' => [
        'factories' => [
            'Geo/Client' => 'Geo\Factory\Service\ClientFactory',
        ],
    ],

    'controllers' => array(
        'factories' => array(
            'Geo\Controller\Index' => 'Geo\Factory\Controller\IndexControllerFactory',
        )
    ),
    'controller_plugins' => array(
        'factories' => array(
        ),
        'invokables' => array(
            'geo/geo' => 'Geo\Controller\Plugin\Geo',
            'geo/photon' => 'Geo\Controller\Plugin\Photon',
            ),
        ),
    
    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'geo' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/geo[/:plugin]',
                            'defaults' => array(
                                'controller' => 'Geo\Controller\Index',
                                'action' => 'index',
                                'module' => 'Geo',
                                'plugin' => 'photon'
                            ),
                            'constraints' => array(
                                'plugin' => '(geo|photon)',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    'form_elements' => [
        'invokables' => [
            'Location' => 'Geo\Form\GeoText',
        ],
        'factories' => [
            'LocationSelect' => 'Geo\Factory\Form\GeoSelectFactory',
        ],
    ],
    
    'view_manager' => array(
    
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'geo/form/GeoText' => __DIR__ . '/../view/form/geotext.phtml',
            'geo/form/GeoHorizontal' => __DIR__ . '/../view/form/geo-horizontal.phtml',
        ),
    
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

];
