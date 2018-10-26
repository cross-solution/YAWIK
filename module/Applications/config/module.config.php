<?php
/**
 * YAWIK
 * Configuration file of the Applications module
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications;

use Applications\Controller\ApplyController;
use Applications\Controller\CommentController;
use Applications\Controller\ConsoleController;
use Applications\Controller\ManageController;
use Applications\Mail\Forward;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'doctrine' => [
       'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Applications\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
               /*
                * All drivers (except DriverChain) require paths to work on. You
                * may set this value as a string (for a single path) or an array
                * for multiple paths.
                * example https://github.com/doctrine/DoctrineORMModule
                */
               'paths' => [ __DIR__ . '/../src/Applications/Entity']
            ],
        ],
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    '\Applications\Repository\Event\JobReferencesUpdateListener',
                    '\Applications\Repository\Event\UpdatePermissionsSubscriber',
                    '\Applications\Repository\Event\UpdateFilesPermissionsSubscriber',
                    '\Applications\Repository\Event\DeleteRemovedAttachmentsSubscriber',
                ],
            ],
        ],
    ],
    
    'Applications' => [
        /*
         * Settings for the application form.
         */
        'dashboard' => [
            'enabled' => true,
            'widgets' => [
                'recentApplications' => [
                    'controller' => 'Applications\Controller\Index',
                ],
            ],
        ],
        'settings' => [
            'entity' => '\Applications\Entity\Settings',
            'navigation_order' => 1,
            'navigation_label' => /*@translate*/ "E-Mail Templates",
            'navigation_class' => 'yk-icon yk-icon-envelope'
        ],
    ],
    
    'service_manager' => [
        'invokables' => [
            'Applications/Options/ModuleOptions' => 'Applications\Options\ModuleOptions',
        ],
        'factories' => [
           'Applications/Options' => 'Applications\Factory\ModuleOptionsFactory',
           'ApplicationRepository' => 'Applications\Repository\Service\ApplicationRepositoryFactory',
           'ApplicationMapper' => 'Applications\Repository\Service\ApplicationMapperFactory',
           'EducationMapper'   => 'Applications\Repository\Service\EducationMapperFactory',
           'Applications/Listener/ApplicationCreated' => 'Applications\Factory\Listener\EventApplicationCreatedFactory',
           'Applications/Listener/ApplicationStatusChangePre' => 'Applications\Factory\Listener\StatusChangeFactory',
           'Applications\Auth\Dependency\ListListener' => 'Applications\Factory\Auth\Dependency\ListListenerFactory',
            Listener\JobSelectValues::class => Factory\Listener\JobSelectValuesFactory::class,
            Listener\LoadDependendEntities::class => InvokableFactory::class,
        ],
        'aliases' => [
           'Applications/Listener/ApplicationStatusChangePost' => 'Applications/Listener/ApplicationStatusChangePre'
        ]
    ],
	
    'controllers' => [
        'invokables' => [
            'Applications\Controller\Index' => 'Applications\Controller\IndexController',
            'Applications\Controller\MultiManage' => 'Applications\Controller\MultimanageController',
        ],
	    'factories' => [
		    'Applications/Controller/Manage' => [ManageController::class,'factory'],
		    'Applications\Controller\Apply' => [ApplyController::class,'factory'],
		    'Applications/CommentController' => [CommentController::class,'factory'],
		    'Applications/Console' => [ConsoleController::class,'factory'],
	    ]
    ],
    
    'acl' => [
        'rules' => [
            'guest' => [
                'allow' => [
                    'route/lang/applications/detail',
                    'Applications\Controller\Manage' => 'detail',
                    'Entity/Application' => [
                        'read' => 'Applications/Access',
                        Entity\ApplicationInterface::PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD => 'Applications/Access',
                    ],
                ],
            ],
            'user' => [
                'allow' => [
                    'route/lang/applications',
                    'Applications\Controller\Manage',
                    'Entity/Application' => [
                        '__ALL__' => 'Applications/Access',
                        
                    ],
                ],
            ],
        ],
        'assertions' => [
            'invokables' => [
                'Applications/Access' => 'Applications\Acl\ApplicationAccessAssertion',
            ],
        ],
    ],
    
    // Navigation
    'navigation' => [
        'default' => [
            'apply' => [
                'label' => 'Applications',
                'route' => 'lang/applications',
                'order' => 20,
                'resource' => 'route/lang/applications',
                'query' => [
                    'clear' => '1'
                ],
                'pages' => [
                    'list' => [
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/applications',
                    ],
                ],
            ],
        ],
    ],
	
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    // Configure the view service manager
    'view_manager' => [
        'template_path_stack' => [
            'Applications' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'applications/error/not-found' => __DIR__ . '/../view/error/not-found.phtml',
            'layout/apply' => __DIR__ . '/../view/layout/layout.phtml',
            'applications/sidebar/manage' => __DIR__ . '/../view/sidebar/manage.phtml',
            'applications/mail/forward' => __DIR__ . '/../view/mail/forward.phtml',
            'applications/detail/pdf' => __DIR__ . '/../view/applications/manage/detail.pdf.phtml',
            'applications/index/disclaimer' => __DIR__ . '/../view/applications/index/disclaimer.phtml',
            'content/applications-privacy-policy' => __DIR__ . '/../view/applications/index/disclaimer.phtml',
        ]
    ],
    'view_helpers' => [
        
    ],

    'view_helper_config' => [
        'headscript' => [
            'lang/applications' => ['Core/js/jquery.barrating.min.js'],
        ],
    ],
    'form_elements' => [
        'invokables' => [
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
             'href' => 'Applications\Form\Element\Ref',

         ],
        'factories' => [
            'Applications/ContactImage' => 'Applications\Factory\Form\ContactImageFactory',
            'Applications/Attachments' => 'Applications\Factory\Form\AttachmentsFactory',
            Form\ApplicationsFilter::class => InvokableFactory::class,
            'Applications\Form\Element\StatusSelect' => Factory\Form\StatusSelectFactory::class,
            Form\Element\JobSelect::class => Factory\Form\JobSelectFactory::class
        ],
     ],

    'form_elements_config' => [
        'Applications/Apply' => [
            /*
             * you can hide form fieldsets, which implements the DisableElementsCapableInterface
             * These are: profiles, facts
             */
            'disable_elements' => ['facts'],
        ],
    ],
     
    'filters' => [
        'invokables' => [
            'Applications/ActionToStatus' => 'Applications\Filter\ActionToStatus',
        ],
        'factories'=> [
            'PaginationQuery/Applications' => '\Applications\Repository\Filter\PaginationQueryFactory'
        ],
    ],
    
    'validators' => [
        'invokables' => [
            'Applications/Application' => 'Applications\Entity\Validator\Application',
        ],
    ],
     
    'mails' => [
        'invokables' => [
            'Applications/StatusChange'   => 'Applications\Mail\StatusChange',
            'Applications/CarbonCopy'     => 'Applications\Mail\ApplicationCarbonCopy',
        ],
        'factories' => [
            'Applications/NewApplication' => 'Applications\Factory\Mail\NewApplicationFactory',
            Mail\Confirmation::class      => Factory\Mail\ConfirmationFactory::class,
            'Applications/Forward'        => [Forward::class,'factory'],
        ],
        'aliases' => [
            'Applications/Confirmation'   => Mail\Confirmation::class,
        ],
    ],

    'paginator_manager' => [
        'factories' => [
            Paginator\JobSelectPaginator::class => Factory\Paginator\JobSelectPaginatorFactory::class,
        ],
    ],

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
        ],
        'Core/Ajax/Events' => ['listeners' => [
            Listener\JobSelectValues::class => ['applications.job-select', true],
        ]],

        'Core/EntityEraser/Dependencies/Events' => [
            'listeners' => [
                Listener\LoadDependendEntities::class => [
                    'events' => [
                        \Core\Service\EntityEraser\DependencyResultEvent::CHECK_DEPENDENCIES => '__invoke',
                        \Core\Service\EntityEraser\DependencyResultEvent::DELETE             => 'onDelete',
                    ],
                ],
            ],
        ]
    ],
];
