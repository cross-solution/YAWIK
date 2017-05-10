<?php

/**
 * YAWIK
 * Configuration file of the Applications module
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
    'doctrine' => array(
       'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Applications\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
               /*
                * All drivers (except DriverChain) require paths to work on. You
                * may set this value as a string (for a single path) or an array
                * for multiple paths.
                * example https://github.com/doctrine/DoctrineORMModule
                */
               'paths' => array( __DIR__ . '/../src/Applications/Entity')
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    '\Applications\Repository\Event\JobReferencesUpdateListener',
                    '\Applications\Repository\Event\UpdatePermissionsSubscriber',
                    '\Applications\Repository\Event\UpdateFilesPermissionsSubscriber',
                    '\Applications\Repository\Event\DeleteRemovedAttachmentsSubscriber',
                ),
            ),
        ),
    ),
    
    'Applications' => array(
        /*
         * Settings for the application form.
         */
        'dashboard' => array(
            'enabled' => true,
            'widgets' => array(
                'recentApplications' => array(
                    'controller' => 'Applications\Controller\Index',
                ),
            ),
        ),
        'settings' => array(
            'entity' => '\Applications\Entity\Settings',
            'navigation_order' => 1,
            'navigation_label' => /*@translate*/ "E-Mail Templates",
            'navigation_class' => 'yk-icon yk-icon-envelope'
        ),
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'Applications/Options/ModuleOptions' => 'Applications\Options\ModuleOptions',
        ),
        'factories' => array(
           'Applications/Options' => 'Applications\Factory\ModuleOptionsFactory',
           'ApplicationRepository' => 'Applications\Repository\Service\ApplicationRepositoryFactory',
           'ApplicationMapper' => 'Applications\Repository\Service\ApplicationMapperFactory',
           'EducationMapper'   => 'Applications\Repository\Service\EducationMapperFactory',
           'Applications/Listener/ApplicationCreated' => 'Applications\Factory\Listener\EventApplicationCreatedFactory',
           'Applications/Listener/ApplicationStatusChangePre' => 'Applications\Factory\Listener\StatusChangeFactory',
           'Applications\Auth\Dependency\ListListener' => 'Applications\Factory\Auth\Dependency\ListListenerFactory'
        ),
        'aliases' => [
           'Applications/Listener/ApplicationStatusChangePost' => 'Applications/Listener/ApplicationStatusChangePre'
        ]
    ),
    'controllers' => array(
        'invokables' => array(
            'Applications\Controller\Index' => 'Applications\Controller\IndexController',
            'Applications\Controller\Apply' => 'Applications\Controller\ApplyController',
            'Applications\Controller\Manage' => 'Applications\Controller\ManageController',
            'Applications/CommentController' => 'Applications\Controller\CommentController',
            'Applications/Console' => 'Applications\Controller\ConsoleController',
            'Applications\Controller\MultiManage' => 'Applications\Controller\MultimanageController',
        ),
    ),
    
    'acl' => array(
        'rules' => array(
            'guest' => array(
                'allow' => array(
                    'route/lang/applications/detail',
                    'Applications\Controller\Manage' => 'detail',
                    'Entity/Application' => array(
                        'read' => 'Applications/Access',
                        Applications\Entity\ApplicationInterface::PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD => 'Applications/Access',
                    ),
                ),
            ),
            'user' => array(
                'allow' => array(
                    'route/lang/applications',
                    'Applications\Controller\Manage',
                    'Entity/Application' => array(
                        '__ALL__' => 'Applications/Access',
                        
                    ),
                ),
            ),
        ),
        'assertions' => array(
            'invokables' => array(
                'Applications/Access' => 'Applications\Acl\ApplicationAccessAssertion',
            ),
        ),
    ),
    
    // Navigation
    'navigation' => array(
        'default' => array(
            'apply' => array(
                'label' => 'Applications',
                'route' => 'lang/applications',
                'order' => 20,
                'resource' => 'route/lang/applications',
                'query' => array(
                    'clear' => '1'
                ),
                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/applications',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    // Configure the view service manager
    'view_manager' => array(
        'template_path_stack' => array(
            'Applications' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'applications/error/not-found' => __DIR__ . '/../view/error/not-found.phtml',
            'layout/apply' => __DIR__ . '/../view/layout/layout.phtml',
            'applications/sidebar/manage' => __DIR__ . '/../view/sidebar/manage.phtml',
            'applications/mail/forward' => __DIR__ . '/../view/mail/forward.phtml',
            'applications/detail/pdf' => __DIR__ . '/../view/applications/manage/detail.pdf.phtml',
            'applications/index/disclaimer' => __DIR__ . '/../view/applications/index/disclaimer.phtml',
            'content/applications-privacy-policy' => __DIR__ . '/../view/applications/index/disclaimer.phtml',
        )
    ),
    'view_helpers' => array(
        
    ),

    'view_helper_config' => array(
        'headscript' => array(
            'lang/applications' => array('Core/js/jquery.barrating.min.js'),
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
             'Applications/Mail' => 'Applications\Form\Mail',
             'Applications/BaseFieldset' => 'Applications\Form\BaseFieldset',
             'Applications/SettingsFieldset' => 'Applications\Form\SettingsFieldset',
             'Applications/CommentForm' => 'Applications\Form\CommentForm',
             'Applications/CommentFieldset' => 'Applications\Form\CommentFieldset',
             'Applications/Apply' => 'Applications\Form\Apply',
             'Applications/Contact' => 'Applications\Form\ContactContainer',
             'Applications/Base'  => 'Applications\Form\Base',
             'Applications/Facts' => 'Applications\Form\Facts',
             'Applications/FactsFieldset' => 'Applications\Form\FactsFieldset',
             'Applications/Attributes' => 'Applications\Form\Attributes',
             'Applications/Filter' => 'Applications\Form\FilterApplication',
             'href' => 'Applications\Form\Element\Ref',
         ),
        'factories' => array(
            'Applications/ContactImage' => 'Applications\Factory\Form\ContactImageFactory',
            'Applications/Attachments' => 'Applications\Factory\Form\AttachmentsFactory',
        ),
     ),

    'form_elements_config' => array(
        'Applications/Apply' => array(
            /*
             * you can hide form fieldsets, which implements the DisableElementsCapableInterface
             * These are: profiles, facts
             */
            'disable_elements' => array('facts'),
        ),
    ),
     
    'filters' => array(
        'invokables' => array(
            'Applications/ActionToStatus' => 'Applications\Filter\ActionToStatus',
        ),
        'factories'=> array(
            'PaginationQuery/Applications' => '\Applications\Repository\Filter\PaginationQueryFactory'
        ),
    ),
    
    'validators' => array(
        'invokables' => array(
            'Applications/Application' => 'Applications\Entity\Validator\Application',
        ),
    ),
     
    'mails' => array(
        'invokables' => array(
            'Applications/Confirmation'   => 'Applications\Mail\Confirmation',
            'Applications/StatusChange'   => 'Applications\Mail\StatusChange',
            'Applications/Forward'        => 'Applications\Mail\Forward',
            'Applications/CarbonCopy'     => 'Applications\Mail\ApplicationCarbonCopy',
        ),
        'factories' => [
            'Applications/NewApplication' => 'Applications\Factory\Mail\NewApplicationFactory',
        ],
    ),
    'event_manager' => [
        'Applications/Events' => [
            'event' => '\Applications\Listener\Events\ApplicationEvent',
            'service' => 'Core/EventManager',
            'listeners' => [
                'Applications/Listener/ApplicationCreated' => [
                    \Applications\Listener\Events\ApplicationEvent::EVENT_APPLICATION_POST_CREATE,
                    /* lazy */ true
                ],
                'Applications/Listener/ApplicationStatusChangePre' => [
                    \Applications\Listener\Events\ApplicationEvent::EVENT_APPLICATION_STATUS_CHANGE,
                    /* lazy */ true,
                    /* priority */ 100,
                    'prepareFormData'

                ],
                'Applications/Listener/ApplicationStatusChangePost' => [
                    \Applications\Listener\Events\ApplicationEvent::EVENT_APPLICATION_STATUS_CHANGE,
                    /* lazy */ true,
                    /* priority */ -10,
                    'sendMail'
                ],

            ]
        ],
        'Auth/Dependency/Manager/Events' => [
            'listeners' => [
                'Applications\Auth\Dependency\ListListener' => [
                    \Auth\Dependency\Manager::EVENT_GET_LISTS,
                    /* lazy */ true
                ]
            ]
        ]
    ],
);
