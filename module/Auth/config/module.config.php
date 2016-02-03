<?php
/**
 * YAWIK
 * Configuration file of the Auth module
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(

    'options' => [
        'Auth/CaptchaOptions' => [
            'class' => '\Auth\Options\CaptchaOptions',
        ],
    ],
    
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Auth\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => array( __DIR__ . '/../src/Auth/Entity'),
            ),
        ),
    ),



    'service_manager' => array(
        'invokables' => array(
            'SessionManager' => '\Zend\Session\SessionManager',
            'Auth\Form\ForgotPasswordInputFilter' => 'Auth\Form\ForgotPasswordInputFilter',
            'Auth\Form\RegisterInputFilter' => 'Auth\Form\RegisterInputFilter',
            'Auth\Form\LoginInputFilter' => 'Auth\Form\LoginInputFilter',
            'Auth\LoginFilter' => 'Auth\Filter\LoginFilter',
            'Auth/Listener/AuthAggregateListener' => 'Auth\Listener\AuthAggregateListener',
        ),
        'factories' => array(
            'Auth/Options' => 'Auth\Factory\ModuleOptionsFactory',
            'HybridAuth' => '\Auth\Factory\Service\HybridAuthFactory',
            'HybridAuthAdapter' => '\Auth\Factory\Adapter\HybridAuthAdapterFactory',
            'ExternalApplicationAdapter' => '\Auth\Factory\Adapter\ExternalApplicationAdapterFactory',
            'Auth/Adapter/UserLogin' => '\Auth\Factory\Adapter\UserAdapterFactory',
            'AuthenticationService' => '\Auth\Factory\Service\AuthenticationServiceFactory',
            'UnauthorizedAccessListener' => '\Auth\Factory\Listener\UnauthorizedAccessListenerFactory',
            'Auth\Listener\MailForgotPassword' => '\Auth\Factory\Listener\MailForgotPasswordFactory',
            'Auth/CheckPermissionsListener' => 'Acl\Listener\CheckPermissionsListenerFactory',
            'Acl' => '\Acl\Factory\Service\AclFactory',
            'Acl\AssertionManager' => 'Acl\Assertion\AssertionManagerFactory',
            'Auth\Form\ForgotPassword' => 'Auth\Factory\Form\ForgotPasswordFactory',
            'Auth\Service\ForgotPassword' => 'Auth\Factory\Service\ForgotPasswordFactory',
            'Auth\Service\UserUniqueTokenGenerator' => 'Auth\Factory\Service\UserUniqueTokenGeneratorFactory',
            'Auth\Service\GotoResetPassword' => 'Auth\Factory\Service\GotoResetPasswordFactory',
            'Auth\Service\Register' => 'Auth\Factory\Service\RegisterFactory',
            'Auth\Service\RegisterConfirmation' => 'Auth\Factory\Service\RegisterConfirmationFactory',
        ),
        'aliases' => array(
            'assertions' => 'Acl\AssertionManager',
            'Auth/UserTokenGenerator' => 'Auth\Service\UserUniqueTokenGenerator',
        )
    ),

    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Manage' => 'Auth\Controller\ManageController',
            'Auth/ManageGroups' => 'Auth\Controller\ManageGroupsController',
            'Auth\Controller\Image' => 'Auth\Controller\ImageController',
            'Auth\Controller\HybridAuth' => 'Auth\Controller\HybridAuthController',
            'Auth/SocialProfiles' => 'Auth\Controller\SocialProfilesController',
        ),
        'factories' => array(
            'Auth\Controller\ForgotPassword' => 'Auth\Factory\Controller\ForgotPasswordControllerFactory',
            'Auth\Controller\GotoResetPassword' => 'Auth\Factory\Controller\GotoResetPasswordControllerFactory',
            'Auth\Controller\Register' => 'Auth\Factory\Controller\RegisterControllerFactory',
            'Auth\Controller\RegisterConfirmation' => 'Auth\Factory\Controller\RegisterConfirmationControllerFactory',
            'Auth\Controller\Password' => 'Auth\Factory\Controller\PasswordControllerFactory',
            'Auth\Controller\Index' => 'Auth\Factory\Controller\IndexControllerFactory',
        )
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'Auth' => '\Auth\Controller\Plugin\Auth',
            'OAuth' => '\Auth\Controller\Plugin\OAuth',
            'Auth/LoginFilter' => 'Auth\Controller\Plugin\LoginFilter',
        ),
        'factories' => array(
            'Auth/SocialProfiles' => 'Auth\Controller\Plugin\Service\SocialProfilesFactory',
            'Acl' => '\Acl\Controller\Plugin\AclFactory',
        ),
        'shared' => array(
            'OAuth' => false,
        )
    ),
    'hybridauth' => array(
        "Facebook" => array (
            "enabled" => true,
            "keys"    => array ( "id" => "", "secret" => "" ),
            "scope"      => 'email, user_about_me, user_birthday, user_hometown, user_website',
            "display" => 'popup',
        ),
        "LinkedIn" => array (
            "enabled" => true,
            "keys"    => array ( "key" => "", "secret" => "" ),
        ),
        "XING" => array (
            "enabled" => true,
            // This is a hack due to bad design of HybridAuth
            // There's no simpler way to include "additional-providers"
            "wrapper" => array (
                'class' => 'Hybrid_Providers_XING',
                'path' => __FILE__,
            ),
            "keys"    => array ( "key" => "", "secret" => "" ),
        ),
        "Github" => array (
            "enabled" => true,
            // This is a hack due to bad design of HybridAuth
            // There's no simpler way to include "additional-providers"
            "wrapper" => array (
                'class' => 'Hybrid_Providers_Github',
                'path' => __FILE__,
            ),
            "keys"    => array ( "key" => "", "secret" => "" ),
        ),

    ),

    'mails' => array(
        'invokables' => array(
            'Auth\Mail\RegisterConfirmation' => 'Auth\Mail\RegisterConfirmation',
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
                    'my' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/my/:action',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Manage',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'my-password' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/my/password',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Password',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'my-groups' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/my/groups[/:action]',
                            'defaults' => array(
                                'controller' => 'Auth/ManageGroups',
                                'action' => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'forgot-password' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth/forgot-password',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\ForgotPassword',
                                'action' => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'goto-reset-password' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth/goto-reset-password/:token/:userId',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\GotoResetPassword',
                                'action' => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'register' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth/register[/:role]',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\Register',
                                'action' => 'index',
                                'role' => 'recruiter'
                            ),
                            'constraints' => array(
                                'role' => '(recruiter|user)',
                            )
                        ),
                        'may_terminate' => true,
                    ),
                    'register-confirmation' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth/register-confirmation/:userId',
                            'defaults' => array(
                                'controller' => 'Auth\Controller\RegisterConfirmation',
                                'action' => 'index'
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
            'auth-social-profiles' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/auth/social-profiles',
                    'defaults' => array(
                        'controller' => 'Auth/SocialProfiles',
                        'action'     => 'fetch',
                    ),
                ),
            ),
            
            'auth-group' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/auth/groups',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Index',
                        'action'     => 'group',
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
            'user-search' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/user/search',
                    'defaults' => array(
                        'controller' => 'Auth/ManageGroups',
                        'action' => 'search-users'
                    ),
                ),
            ),
            'test-hybrid' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/testhybrid',
                    'defaults' => array(
                        'controller' => 'Auth/SocialProfiles',
                        'action' => 'testhybrid',
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
     *      1.1 the "null" value: allow on all resources.
     *      2. a key => string pair:
     *          if key is "__ALL__" rule apply to all resources.
     *          string is the privilege name
     *      3. a key => array pair:
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
            'admin' => 'recruiter',
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
                    'route/lang/forgot-password',
                    'route/lang/goto-reset-password',
                    'route/lang/register',
                    'route/lang/register-confirmation',
                ),
            ),
            'user' => array(
                'allow' => array(
                    'route/auth-logout',
                    'route/lang/my',
                    'route/lang/my-password'
                ),
                'deny' => array(
                   // 'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-extern',
                    'route/lang/forgot-password',
                    'route/lang/goto-reset-password',
                    'route/lang/register',
                    'route/lang/register-confirmation',
                ),
            ),
            'recruiter' => array(
                'allow' => array(
                    'route/lang/my-groups'
                ),
            ),
            'admin' => array(
                'allow' => "__ALL__",
//                'deny' => array(
//                    'route/lang/auth',
//                    'route/auth-provider',
//                    'route/auth-hauth',
//                    'route/auth-extern',
//                ),
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
            'form/auth/contact.form' => __DIR__ . '/../view/form/contact.form.phtml',
            'form/auth/contact.view' => __DIR__ . '/../view/form/contact.view.phtml',
            'auth/error/social-profiles-unconfigured' => __DIR__ . '/../view/error/social-profiles-unconfigured.phtml',
            'auth/form/user-info-container' => __DIR__ . '/../view/form/user-info-container.phtml',
            'auth/form/userselect' => __DIR__ . '/../view/form/userselect.phtml',
            'auth/form/social-profiles-fieldset' => __DIR__ . '/../view/form/social-profiles-fieldset.phtml',
            'auth/form/social-profiles-button' => __DIR__ . '/../view/form/social-profiles-button.phtml',
            'auth/sidebar/groups-menu' => __DIR__ . '/../view/sidebar/groups-menu.phtml',
            'mail/first-external-login' => __DIR__ . '/../view/mail/first-external-login.phtml',
            'mail/first-socialmedia-login' => __DIR__ . '/../view/mail/first-socialmedia-login.phtml',
            'mail/forgotPassword' =>  __DIR__ . '/../view/mail/forgot-password.phtml',
            'mail/register' =>  __DIR__ . '/../view/mail/register.phtml',
        ),
    
        'template_path_stack' => array(
            'Auth' => __DIR__ . '/../view',
        ),
    ),
    
    'filters' => array(
        'invokables' => array(
            'Auth/StripQueryParams' => '\Auth\Filter\StripQueryParams',
            'Auth/Entity/UserToSearchResult' => '\Auth\Entity\Filter\UserToSearchResult',
        ),
    ),
    
    'validators' => array(
        'factories' => array(
            'Auth/Form/UniqueGroupName' => 'Auth\Form\Validator\UniqueGroupNameFactory',
        ),
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'buildReferer' => '\Auth\View\Helper\BuildReferer',
            'loginInfo' => '\Auth\View\Helper\LoginInfo',
        ),
        'factories' => array(
            'auth' => '\Auth\Factory\View\Helper\AuthFactory',
            'acl'  => '\Acl\Factory\View\Helper\AclFactory',
         ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'Auth/Login' => 'Auth\Form\Login',
            'user-profile' => 'Auth\Form\UserProfile',
            'user-password' => 'Auth\Form\UserPassword',
            'Auth/UserPasswordFieldset' => 'Auth\Form\UserPasswordFieldset',
            'Auth/UserBase' => 'Auth\Form\UserBase',
            'Auth/UserBaseFieldset' => 'Auth\Form\UserBaseFieldset',
            'Auth/Group' => 'Auth\Form\Group',
            'Auth/Group/Data' => 'Auth\Form\GroupFieldset',
            'Auth/Group/Users' => 'Auth\Form\GroupUsersCollection',
            'Auth/Group/User'  => 'Auth\Form\GroupUserElement',
            'Auth/SocialProfilesButton' => 'Auth\Form\Element\SocialProfilesButton',
            'Auth/SocialProfiles' => 'Auth\Form\SocialProfiles',
            'Auth/UserInfoContainer' => 'Auth\Form\UserInfoContainer',
            'Auth/UserInfo' => 'Auth\Form\UserInfo',
            'Auth/UserProfileContainer' => 'Auth\Form\UserProfileContainer',
        ),
        'factories' => array(
            'Auth/RoleSelect' => 'Auth\Factory\Form\RoleSelectFactory',
            'Auth/UserInfoFieldset' => 'Auth\Factory\Form\UserInfoFieldsetFactory',
            'Auth/SocialProfilesFieldset' => 'Auth\Factory\Form\SocialProfilesFieldsetFactory',
            'Auth/UserImage' => 'Auth\Form\UserImageFactory',
            'Auth\Form\Login' => 'Auth\Factory\Form\LoginFactory',
            'Auth\Form\Register' => 'Auth\Factory\Form\RegisterFactory',
            'Auth/UserSearchbar' => 'Auth\Factory\Form\Element\UserSearchbarFactory',
        )
    ),
);
