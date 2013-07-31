<?php
/**
 * Cross Applicant Management
 * Configuration file of the Auth module
 * 
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(
    
    'service_manager' => array(
        'invokables' => array(
            'SessionManager' => '\Zend\Session\SessionManager',
            'AuthenticationService' => '\Zend\Authentication\AuthenticationService',
        ),
        'factories' => array(
            'HybridAuth' => '\Auth\Service\HybridAuthFactory',
            'HybridAuthAdapter' => '\Auth\Service\HybridAuthAdapterFactory',
            'ExternalApplicationAdapter' => '\Auth\Service\ExternalApplicationAdapterFactory',
        ),
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController',
            'Auth\Controller\HybridAuth' => 'Auth\Controller\HybridAuthController',
        ),
    ),
    
    'hybridauth' => array(
        "Facebook" => array (
            "enabled" => true,
            "keys"    => array ( "id" => "", "secret" => "" ),
            "scope"	  => 'email, user_about_me, user_birthday, user_hometown, user_website',
        ),
        "LinkedIn" => array (
            "enabled" => true,
            "keys"    => array ( "key" => "", "secret" => "" ),
        ),
        "XING" => array (
            "enabled" => true,
            // This is a hack due to bad design of Hybridauth
            // There's no simpler way to include "additional-providers"
            "wrapper" => array ( 
                'class' => 'Hybrid_Providers_XING',
                'path' => __FILE__,
            ),
            "keys"    => array ( "key" => "", "secret" => "" ),
        ),
    ),
    
    // Module specific configuration
    
    'Auth' => array(
        // Allowed external Applications
        // applications[USERPOSTFIX] => AppKey
        'external_applications' => array(
            'ams' => 'AmsAppKey',
        ),
    ),
    
    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'auth' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/login',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Index',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
            'auth-provider' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/login/:provider',
                    'constraints' => array(
                       // 'provider' => '.+',
                    ),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'login'
                     ),
                ),
            ),
            'auth-hauth' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login/hauth',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\HybridAuth',
                        'action' => 'index'
                    ),
                ),
            ),
            // This route must be after auth-provider for the
            // last in first out order of the route stack!
            // @TODO implement auth-provider and auth-extern as child routes
            //       to a new auth-login route.
            'auth-extern' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login/extern',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action'     => 'login-extern',
                        'forceJson'  => true,
                    ),
                ),
                'may_terminate' => true,
            ),
            'auth-logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'logout',
                    ),
                ),
            ),
        ),
    ),
    
    // Navigation
//     'navigation' => array(
//         'default' => array( 
//             'login' => array(
//                 'label' => 'Login',
//                 'route' => 'auth',
//                 'pages' => array(
//                     'facebook' => array(
//                         'label' => 'Facebook',
//                         'route' => 'auth/auth-providers',
//                         'params' => array(
//                             'provider' => 'facebook'
//                         ),
//                      ),
//                 ),
//             ),
//         ),
//     ),
    
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
    // Configure the view service manager
    'view_manager' => array(
        'template_path_stack' => array(
            'Auth' => __DIR__ . '/../view',
        ),
    ),
    
    'view_helpers' => array(
        'factories' => array(
            'auth' => '\Auth\Service\AuthViewHelperFactory',
         ),
    ),
    
    
);
