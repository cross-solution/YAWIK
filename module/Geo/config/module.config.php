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
                            'route' => '/geo',
                            'defaults' => array(
                                'controller' => 'Geo\Controller\Index',
                                'action' => 'index',
                                'module' => 'Geo',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    
   'form_elements' => array(
        'invokables' => array(
            'Location' => 'Geo\Form\GeoText',
        ),
    ),
    
   'view_manager' => array(
    
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
   
            'geo/form/GeoText' => __DIR__ . '/../view/form/geotext.phtml',
            //'form/div-wrapper-fieldset' => __DIR__ . '/../view/form/div-wrapper-fieldset.phtml',
        ),
    
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
    // spezifische Daten
    'cross_geoapi_url' => 'http://api.cross-solution.de/geo',
    
     'view_helper_config' => array(
        'headscript' => array(
            'Geo/js/jquery.geolocationfield.js',
            //'js/typeahead.jquery.min.js',
            //'js/bloodhound.min.js',
            'Geo/js/typeahead.jquery.min.js',
            'Geo/js/bloodhound.min.js',
        ),
    ),
);