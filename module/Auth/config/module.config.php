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
        ),
        'factories' => array(
            'HybridAuth' => '\Auth\Service\HybridAuthFactory',
            'HybridAuthAdapter' => '\Auth\Service\HybridAuthAdapterFactory',
            'ExternalApplicationAdapter' => '\Auth\Service\ExternalApplicationAdapterFactory',
            'auth-login-adapter' => '\Auth\Service\UserAdapterFactory',
            'AuthenticationService' => '\Auth\Service\AuthenticationServiceFactory',
            'UnauthorizedAccessListener' => '\Auth\Service\UnauthorizedAccessListenerFactory',
            'Auth/CheckPermissionsListener' => 'Acl\Listener\CheckPermissionsListenerFactory',
            'Acl' => '\Acl\Service\AclFactory',
            'Acl/AssertionManager' => 'Acl\Assertion\AssertionManagerFactory',
        ),
        'aliases' => array(
            'assertions' => 'Acl/AssertionManager',
        )
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
        'factories' => array(
            'Acl' => '\Acl\Controller\Plugin\AclFactory',
        )
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
                    'Save Application Confirmation' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/save/applicationconfirmation',
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
    
    /*
     * Acl definitions.
     * Format
     * array($ROLE[:$PARENT] => $RESOURCES);
     * 
     * $ROLE: Role name
     * $PARENT: Coma separated list of roles to inherit from.
     * $RESOURCES: array of resources
     *      a resource is 
     *      1. a string: taken as resource name
     *                   (when prefixed with "!", a deny rule is created.)
     *      1.1 the "null" value: allow on all resources.
     *      2. a key => string pair:
     *          key is the resource name (optionally prefixed with "!")
     *          if key is "__ALL__" rule apply to all resources.
     *          string is the privilege name
     *      3. a key => array pair:
     *              key is the resource name (optionally prefixed with "!")
     *              array are the privileges which each of is
     *              1. a string: Taken as privilege name
     *              2. a key => string pair:
     *                  key is the privilege name
     *                  string is the name of the assertion class to instantiate and use with this rule.
     *              3. a key => array pair:
     *                  key is the privilege name
     *                  array is:
     *                      index 0: Name of the assertion class,
     *                      index 1: array of parameters to pass to the constructor of the assertion.
     *                  
     */
    'acl' => array(
        'roles' => array(
            'guest',
            'user' => 'guest',
            'recruiter' => 'user',
            'admin'
        ),
        
        'public_roles' => array(
            /*@translate*/ 'user', 
            /*@translate*/ 'recruiter',
        ),
        
        'rules' => array(
            'guest' => array(
                'allow' => array(
                    'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-hauth',
                    'route/auth-extern',
                ),
            ),
            'user' => array(
                'allow' => array(
                    'route/auth-logout',
                ),
                'deny' => array( 
                    'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-hauth',
                    'route/auth-extern',
                ),
            ),
            'admin' => array(
                'allow' => "__ALL__",
                'deny' => array(
                    'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-hauth',
                    'route/auth-extern',
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
            'acl'  => '\Acl\View\Helper\AclFactory',
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
            'Auth/UserBaseFieldset' => 'Auth\Form\UserBaseFieldset', 
        ),
        'factories' => array(
            'Auth/RoleSelect' => 'Auth\Form\RoleSelectFactory',
        )
    ),
);
