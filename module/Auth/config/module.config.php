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
            //'AuthenticationService' => '\Zend\Authentication\AuthenticationService',
        ),
        'factories' => array(
            'HybridAuth' => '\Auth\Service\HybridAuthFactory',
            'HybridAuthAdapter' => '\Auth\Service\HybridAuthAdapterFactory',
            'ExternalApplicationAdapter' => '\Auth\Service\ExternalApplicationAdapterFactory',
            'auth-login-adapter' => '\Auth\Service\UserAdapterFactory',
            'AuthenticationService' => '\Auth\Service\AuthenticationServiceFactory',
            'UnauthorizedAccessListener' => '\Auth\Service\UnauthorizedAccessListenerFactory',
            'Acl' => '\Acl\Service\AclFactory',
        ),
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController',
            'Auth\Controller\Manage' => 'Auth\Controller\ManageController',
            'Auth\Controller\Image' => 'Auth\Controller\ImageController',
            'Auth\Controller\HybridAuth' => 'Auth\Controller\HybridAuthController',
        ),
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'Auth' => '\Auth\Controller\Plugin\Auth',
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
        // all the Information for the Module Settings
        // der erste Teil ist der NameSpace, also der Modulname
        'settings' => array(
            'entity' => '\Auth\Entity\Settings',
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
                    'manage-profile' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/my/profile',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Manage',
                                'action' => 'my-profile',
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
            'user-image' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/image/:id',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Image',
                        'action' => 'index',
                        'id' => 0,
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
        'template_map' => array(
            'form/auth/my-profile' => __DIR__ . '/../view/form/my-profile.phtml',
        ),
    
        'template_path_stack' => array(
            'Auth' => __DIR__ . '/../view',
        ),
    ),
    
    'filters' => array(
        'invokables' => array(
            'Auth/StripQueryParams' => '\Auth\Filter\StripQueryParams',
        ),
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'stripQueryParams' => '\Auth\View\Helper\StripQueryParams',
        ),   
        'factories' => array(
            'auth' => '\Auth\Service\AuthViewHelperFactory',
         ),
    ),
    
    'repositories' => array(
        'invokables' => array(
            'user' => 'Auth\Repository\User',
            
        ),
    ),
    
    'mappers' => array(
        'factories' => array(
            'user' => 'Auth\Repository\Mapper\UserMapperFactory',
            'user-file' => 'Auth\Repository\Mapper\FileMapperFactory',
         ),
    ),
    
    'entity_builders' => array(
        'factories' => array(
            'user' => 'Auth\Repository\EntityBuilder\UserBuilderFactory',
            'auth-info' => 'Auth\Repository\EntityBuilder\InfoBuilderFactory',
            'user-file' => 'Auth\Repository\EntityBuilder\FileBuilderFactory',
                
        ),
    ),
    
    'form_elements' => array(
        'invokables' => array( 
            'user-login' => 'Auth\Form\Login',
            'user-profile' => 'Auth\Form\UserProfile',
            'user-info-fieldset' => 'Auth\Form\UserInfoFieldset',
            'settings\auth' => 'Auth\Form\Settings',
            'settings-auth-fieldset' => 'Auth\Form\SettingsFieldset',
        ),
    ),
    
    'settings' => array(
        __namespace__ => array(
            'Settings/Entity' => 'abc'
        ),
    ),
    
);
