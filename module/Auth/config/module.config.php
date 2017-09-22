<?php
/**
 * YAWIK
 * Configuration file of the Auth module
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth;

use Acl\Assertion\AssertionManagerFactory;
use Acl\Factory\Service\AclFactory;
use Auth\Controller\ManageController;
use Auth\Controller\ManageGroupsController;
use Auth\Controller\Plugin\Auth;
use Auth\Listener\Events\AuthEvent;

return [

    'options' => [
        'Auth/Options' => [
            'class' => Options\ModuleOptions::class,
        ],
        'Auth/CaptchaOptions' => [
            'class' => '\Auth\Options\CaptchaOptions',
        ],
    ],
    
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Auth\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => [ __DIR__ . '/../src/Auth/Entity'],
            ],
        ],
    ],



    'service_manager' => [
        'invokables' => [
            'SessionManager' => '\Zend\Session\SessionManager',
            'Auth\Form\ForgotPasswordInputFilter' => 'Auth\Form\ForgotPasswordInputFilter',
            'Auth\Form\RegisterInputFilter' => 'Auth\Form\RegisterInputFilter',
            'Auth\Form\LoginInputFilter' => 'Auth\Form\LoginInputFilter',
            'Auth\LoginFilter' => 'Auth\Filter\LoginFilter',
        ],
        'factories' => [
            'HybridAuth' => '\Auth\Factory\Service\HybridAuthFactory',
            'HybridAuthAdapter' => '\Auth\Factory\Adapter\HybridAuthAdapterFactory',
            'ExternalApplicationAdapter' => '\Auth\Factory\Adapter\ExternalApplicationAdapterFactory',
            'Auth/Adapter/UserLogin' => '\Auth\Factory\Adapter\UserAdapterFactory',
            'AuthenticationService' => '\Auth\Factory\Service\AuthenticationServiceFactory',
            'UnauthorizedAccessListener' => '\Auth\Factory\Listener\ExceptionStrategyFactory',
            'DeactivatedUserListener' => '\Auth\Factory\Listener\ExceptionStrategyFactory',
            Listener\MailForgotPassword::class => '\Auth\Factory\Listener\MailForgotPasswordFactory',
            Listener\SendRegistrationNotifications::class => Factory\Listener\SendRegistrationNotificationsFactory::class,
            'Auth/CheckPermissionsListener' => 'Acl\Listener\CheckPermissionsListenerFactory',
            'Acl' => AclFactory::class,
            'Acl\AssertionManager' => AssertionManagerFactory::class,
            'Auth\Form\ForgotPassword' => 'Auth\Factory\Form\ForgotPasswordFactory',
            'Auth\Service\ForgotPassword' => 'Auth\Factory\Service\ForgotPasswordFactory',
            'Auth\Service\UserUniqueTokenGenerator' => 'Auth\Factory\Service\UserUniqueTokenGeneratorFactory',
            'Auth\Service\GotoResetPassword' => 'Auth\Factory\Service\GotoResetPasswordFactory',
            'Auth\Service\Register' => 'Auth\Factory\Service\RegisterFactory',
            'Auth\Service\RegisterConfirmation' => 'Auth\Factory\Service\RegisterConfirmationFactory',
            'Auth/Dependency/Manager' => 'Auth\Factory\Dependency\ManagerFactory',
        ],
        'aliases' => [
            'assertions' => 'Acl\AssertionManager',
            'Auth/UserTokenGenerator' => 'Auth\Service\UserUniqueTokenGenerator',
	        'acl' => 'Acl'
        ]
    ],

    'controllers' => [
        'invokables' => [
            'Auth\Controller\Image' => 'Auth\Controller\ImageController',
            'Auth\Controller\HybridAuth' => 'Auth\Controller\HybridAuthController',
            'Auth/SocialProfiles' => 'Auth\Controller\SocialProfilesController',
        ],
        'factories' => [
	        'Auth\Controller\Manage' => [ManageController::class,'factory'],
	        'Auth/ManageGroups' => [ManageGroupsController::class,'factory'],
            'Auth\Controller\ForgotPassword' => 'Auth\Factory\Controller\ForgotPasswordControllerFactory',
            'Auth\Controller\GotoResetPassword' => 'Auth\Factory\Controller\GotoResetPasswordControllerFactory',
            'Auth\Controller\Register' => 'Auth\Factory\Controller\RegisterControllerFactory',
            'Auth\Controller\RegisterConfirmation' => 'Auth\Factory\Controller\RegisterConfirmationControllerFactory',
            'Auth\Controller\Password' => 'Auth\Factory\Controller\PasswordControllerFactory',
            'Auth\Controller\Index' => 'Auth\Factory\Controller\IndexControllerFactory',
            'Auth/Users' => 'Auth\Factory\Controller\UsersControllerFactory',
            'Auth\Controller\Remove' => 'Auth\Factory\Controller\RemoveControllerFactory'
        ]
    ],
    
    'controller_plugins' => [
        'factories' => [
            'Auth/SocialProfiles' => 'Auth\Controller\Plugin\Service\SocialProfilesFactory',
            'Acl' => '\Acl\Controller\Plugin\AclFactory',
            'Auth/LoginFilter' => 'Auth\Controller\Plugin\LoginFilter::factory',
            'OAuth' => [\Auth\Controller\Plugin\OAuth::class,'factory'],
            'Auth' => [Auth::class,'factory'],
            'Auth/User/Switcher' => 'Auth\Factory\Controller\Plugin\UserSwitcherFactory',
        ],
        'shared' => [
            'OAuth' => false,
        ],
	    'aliases' => [
	    	'acl' => 'Acl',
		    'auth' => 'Auth'
	    ]
    ],
    'hybridauth' => [
        "Facebook" => [
            "enabled" => true,
            "keys"    => [ "id" => "", "secret" => "" ],
            "scope"      => 'email, user_about_me, user_birthday, user_hometown, user_website',
            "display" => 'popup',
        ],
        "LinkedIn" => [
            "enabled" => true,
            "keys"    => [ "key" => "", "secret" => "" ],
        ],
        "XING" => [
            "enabled" => true,
            // This is a hack due to bad design of HybridAuth
            // There's no simpler way to include "additional-providers"
            "wrapper" => [
                'class' => 'Hybrid_Providers_XING',
                'path' => __FILE__,
            ],
            "keys"    => [ "key" => "", "secret" => "" ],
        ],
        "Github" => [
            "enabled" => true,
            // This is a hack due to bad design of HybridAuth
            // There's no simpler way to include "additional-providers"
            "wrapper" => [
                'class' => 'Hybrid_Providers_Github',
                'path' => __FILE__,
            ],
            "keys"    => [ "key" => "", "secret" => "" ],
        ],

    ],

    'mails' => [
        'invokables' => [
            'Auth\Mail\RegisterConfirmation' => 'Auth\Mail\RegisterConfirmation',
        ],
    ],

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
    'acl' => [
        'roles' => [
            'guest',
            'user' => 'guest',
            'recruiter' => 'user',
            'admin' => 'recruiter',
            'employee_recruiter' => 'recruiter',
        ],
        
        'public_roles' => [
            /*@translate*/ 'user',
            /*@translate*/ 'recruiter',
        ],
        
        'rules' => [
            'guest' => [
                'allow' => [
                    'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-hauth',
                    'route/auth-extern',
                    'route/lang/forgot-password',
                    'route/lang/goto-reset-password',
                    'route/lang/register',
                    'route/lang/register-confirmation',
                ],
            ],
            'user' => [
                'allow' => [
                    'route/auth-logout',
                    'route/lang/my',
                    'route/lang/my-password',
                    'route/lang/user-remove',
                    'Auth/Users' => 'switch',
                ],
                'deny' => [
                   // 'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-extern',
                    'route/lang/forgot-password',
                    'route/lang/goto-reset-password',
                    'route/lang/register',
                    'route/lang/register-confirmation',
                ],
            ],
            'recruiter' => [
                'allow' => [
                    'route/lang/my-groups',
                ],
            ],
            'admin' => [
                'allow' => [
                    '__ALL__',
                    'Users',
                    'route/lang/user-list',
                    'route/lang/user-edit',
                    'Auth/Users' => '__ALL__',
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    
    // Configure the view service manager
    'view_manager' => [
        'template_map' => [
            'form/auth/contact.form' => __DIR__ . '/../view/form/contact.form.phtml',
            'form/auth/contact.view' => __DIR__ . '/../view/form/contact.view.phtml',
            'form/auth/status.form' => __DIR__ . '/../view/form/status.form.phtml',
            'form/auth/status.view' => __DIR__ . '/../view/form/status.view.phtml',
            'auth/error/social-profiles-unconfigured' => __DIR__ . '/../view/error/social-profiles-unconfigured.phtml',
            'auth/form/user-info-container' => __DIR__ . '/../view/form/user-info-container.phtml',
            'auth/form/user-status-container' => __DIR__ . '/../view/form/user-status-container.phtml',
            'auth/form/userselect' => __DIR__ . '/../view/form/userselect.phtml',
            'auth/form/social-profiles-fieldset' => __DIR__ . '/../view/form/social-profiles-fieldset.phtml',
            'auth/form/social-profiles-button' => __DIR__ . '/../view/form/social-profiles-button.phtml',
            'auth/sidebar/groups-menu' => __DIR__ . '/../view/sidebar/groups-menu.phtml',
            'mail/first-external-login' => __DIR__ . '/../view/mail/first-external-login.phtml',
            'mail/first-socialmedia-login' => __DIR__ . '/../view/mail/first-socialmedia-login.phtml',
            'mail/forgotPassword' =>  __DIR__ . '/../view/mail/forgot-password.phtml',
            'mail/forgotPassword.en' =>  __DIR__ . '/../view/mail/forgot-password.en.phtml',
            'mail/register' =>  __DIR__ . '/../view/mail/register.phtml',
            'auth/mail/new-registration' => __DIR__ . '/../view/mail/new-registration.phtml',
            'auth/mail/new-registration.de' => __DIR__ . '/../view/mail/new-registration.de.phtml',
            'auth/mail/user-confirmed' => __DIR__ . '/../view/mail/user-confirmed.phtml',
            'auth/mail/user-confirmed.de' => __DIR__ . '/../view/mail/user-confirmed.de.phtml',
        ],
    
        'template_path_stack' => [
            'Auth' => __DIR__ . '/../view',
        ],
    ],
    
    'filters' => [
        'invokables' => [
            'Auth/StripQueryParams' => '\Auth\Filter\StripQueryParams',
            'Auth/Entity/UserToSearchResult' => '\Auth\Entity\Filter\UserToSearchResult',
            'PaginationQuery/Auth/User'   => 'Auth\Repository\Filter\PaginationSearchUsers',
        ],
    ],
    
    'validators' => [
        'factories' => [
            'Auth/Form/UniqueGroupName' => 'Auth\Form\Validator\UniqueGroupNameFactory',
        ],
    ],
    
    'view_helpers' => [
        'invokables' => [
            'buildReferer' => '\Auth\View\Helper\BuildReferer',
            'loginInfo' => '\Auth\View\Helper\LoginInfo',
        ],
        'factories' => [
            'auth' => '\Auth\Factory\View\Helper\AuthFactory',
            'acl'  => '\Acl\Factory\View\Helper\AclFactory',
         ],
    ],
    
    'form_elements' => [
        'invokables' => [
            'Auth/Login' => 'Auth\Form\Login',
            'user-profile' => 'Auth\Form\UserProfile',
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
            'Auth/UserStatusContainer' => 'Auth\Form\UserStatusContainer',
            'Auth/UserStatus' => 'Auth\Form\UserStatus'
        ],
        'factories' => [
            'Auth/RoleSelect' => 'Auth\Factory\Form\RoleSelectFactory',
            'Auth/UserInfoFieldset' => 'Auth\Factory\Form\UserInfoFieldsetFactory',
            'Auth/UserStatusFieldset' => 'Auth\Factory\Form\UserStatusFieldsetFactory',
            'Auth/SocialProfilesFieldset' => 'Auth\Factory\Form\SocialProfilesFieldsetFactory',
            'Auth/UserImage' => 'Auth\Form\UserImageFactory',
            'Auth\Form\Login' => 'Auth\Factory\Form\LoginFactory',
            'Auth\Form\Register' => 'Auth\Factory\Form\RegisterFactory',
            'user-password' => [\Auth\Form\UserPassword::class,'factory'],
        ]
    ],

    'event_manager' => [
        'Auth/Events' => [
            'service' => 'Core/EventManager',
            'event' => AuthEvent::class,
            'listeners' => [
                Listener\MailForgotPassword::class => [ AuthEvent::EVENT_AUTH_NEWPASSWORD, 10, true ],
                Listener\SendRegistrationNotifications::class => [
                    [ AuthEvent::EVENT_USER_REGISTERED, AuthEvent::EVENT_USER_CONFIRMED ],
                    true
                ],
            ],
        ]
    ]
];
