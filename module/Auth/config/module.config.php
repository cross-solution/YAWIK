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
    
    // Routes
    'router' => array(
        'routes' => array(
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
                'child_routes' => array(
                    'providers' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/:provider',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Index',
                                'action' => 'login'
                             ),
                        ),
                    ),
                    'hauth' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/hauth',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\HybridAuth',
                                'action' => 'index'
                             ),
                        ),
                    ),
                ),
            ),
            'logout' => array(
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
