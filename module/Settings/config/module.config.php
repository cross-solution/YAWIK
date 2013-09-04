<?php
/**
 * Cross Applicant Management
 * Configuration file of the Core module
 * 
 * This file intents to provide the configuration for all other modules
 * as well (convention over configuration).
 * Having said that, you may always overwrite or extend the configuration
 * in your own modules configuration file(s) (or via the config autoloading).
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(
    
     // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'settings' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/settings',
                            'defaults' => array(
                                'controller' => 'Settings\Controller\Index',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    
    
    
    // Navigation-Konfig für die main_navigation
    'navigation' => array(
        'default' => array(
            'settings' => array(
                'label' => /*@translate*/ 'Settings',
                'route' => 'lang/settings',
            ),
        ),
    ),
    
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            'Settings\Controller\Index' => 'Settings\Controller\IndexController'
        ),
    ),
   
    // Configure the view service manager
    'view_manager' => array(
        // Map template to files. Speeds up the lookup through the template stack. 
        'template_map' => array(
        ),
        
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
    'view_helpers' => array(
        'invokables' => array(
        ),
        'factories' => array(
        ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'Settings' => '\Settings\Form\Settings',
        ),
        'factories' => array(
        ),
    ),
    
     'service_manager' => array(
        'factories' => array(),
        'initializers' => array(),
        'shared' => array(),
        'aliases' => array(),
    ),
    
    'repositories' => array(
        'factories' => array('SettingsRepository' => '\Settings\Repository\Service\SettingsFactory'),
        'initializers' => array(),
        'shared' => array(),
        'aliases' => array(),
    ),
    
    'controller_plugins' => array(
        'factories' => array('settings' => '\Settings\Repository\Service\SettingsPluginFactory'),
    ),
);
