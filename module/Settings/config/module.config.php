<?php
/**
 * YAWIK
 * Configuration file of the Core module
 * 
 * This file intents to provide the configuration for all other modules
 * as well (convention over configuration).
 * Having said that, you may always overwrite or extend the configuration
 * in your own modules configuration file(s) (or via the config autoloading).
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
        
        'doctrine' => array(
                'driver' => array(
                        'odm_default' => array(
                                'drivers' => array(
                                        'Settings\Entity' => 'annotation',
                                ),
                        ),
                ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    'Settings/InjectEntityResolverListener',
                 ),
            ),
        ),
    ),
    
		
	// Translations
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'settings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/settings[/:module]',
                            'defaults' => array(
                                'controller' => 'Settings\Controller\Index',
                                'action' => 'index',
                                'module' => 'Core',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    
    'acl' => array('rules' => array(
        'user' => array(
            'allow' => array(
                'route/lang/settings',
                'Settings\Controller\Index',
            ),
        ),
    )),
    
    // Navigation-Konfig für die main_navigation
    'navigation' => array(
        'default' => array(
            'settings' => array(
                'label' => /*@translate*/ 'Settings',
                'route' => 'lang/settings',
                'resource' => 'route/lang/settings',
                'order' => 100,
                'params' => array('module' => null),
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
    
     'service_manager' => array(
        'invokables' => array(
            'Settings/InjectEntityResolverListener' => 'Settings\Repository\Event\InjectSettingsEntityResolverListener',
    ),
        'factories' => array(
            'Settings' => '\Settings\Settings\SettingsFactory',
            'Settings/EntityResolver' => '\Settings\Repository\SettingsEntityResolverFactory',
        ),
        'initializers' => array(),
        'shared' => array(),
        'aliases' => array(),
    ),
    
    'controller_plugins' => array(
        'factories' => array('settings' => '\Settings\Controller\Plugin\SettingsFactory'),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'Settings/Form' => '\Settings\Form\AbstractSettingsForm',
            'Settings/Fieldset' => '\Settings\Form\SettingsFieldset',
        ),
    ),
    
    
);
