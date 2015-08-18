<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Geo\Controller\Index' => 'Geo\Controller\IndexController',
        ),
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
                            'route' => '/geo/:plugin',
                            'defaults' => array(
                                'controller' => 'Geo\Controller\Index',
                                'action' => 'index',
                                'module' => 'Geo',
                                'defaults' => array(
                                    'defaults' => array(
                                        'plugin' => 'geo'
                                    ),
                                    'constraints' => array(
                                        'plugin' => '(geo|photon)',
                                    ),
                                ),
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    'form_elements' => array(
        'factory' => array(
            'Location' => 'Geo\Factory\Form\GeoTextFactory',
         ),
    ),
    
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
    
    // spezifische Daten
    'geocoder_cross_url' => 'http://api.cross-solution.de/geo',
    'geocoder_photon_url' => 'http://www2.mediaintown.de:2322/api',

);