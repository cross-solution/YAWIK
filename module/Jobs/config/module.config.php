<?php
namespace Jobs;
use Jobs\Controller\AdminCategoriesController;
use Jobs\Controller\AdminController;
use Jobs\Controller\ConsoleController;
use Jobs\Form\InputFilter\JobLocationEdit;
use Jobs\Listener\Publisher;

return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Jobs\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => [ __DIR__ . '/../src/Jobs/Entity'],
            ],
        ],
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    '\Jobs\Repository\Event\UpdatePermissionsSubscriber',
                ],
            ],
        ],
    ],

    'options' => [
        'Jobs/JobboardSearchOptions' => [ 'class' => '\Jobs\Options\JobboardSearchOptions' ],
        'Jobs/BaseFieldsetOptions' => [ 'class' => '\Jobs\Options\BaseFieldsetOptions' ],
    ],

    'Jobs' => [
        'dashboard' => [
            'enabled' => true,
            'widgets' => [
                'recentJobs' => [
                    'controller' => 'Jobs/Index',
                    'params' => [
                        'type' => 'recent'
                    ],
                ],
            ],
        ],
    ],

    // Translations
    'translator' => [
            'translation_file_patterns' => [
                    [
                            'type'     => 'gettext',
                            'base_dir' => __DIR__ . '/../language',
                            'pattern'  => '%s.mo',
                    ],
            ],
    ],

    'acl' => [
        'rules' => [
            'recruiter' => [
                'allow' => [
                    'Jobs',
                    'JobList',
                    'Jobs/Manage' => [
                        'delete',
                        'edit',
                        'deactivate',
                        'completion',
                        'deactivate',
                        'template',
                        'new' => 'Jobs/Create',
                        'history',
                        'channel-list'
                    ],
                    'JobboardRecruiter',
                    'route/lang/jobs/manage',
                    'route/lang/jobs/template',
                    'Entity/Jobs/Job' => [
                        'edit' => 'Jobs/Write',
                    ],
                ],
                'deny' => [
                    'Jobboard',
                    'route/lang/jobs/approval',
                ],
            ],
            'guest' => [
                'allow' => [
                    'Jobboard',
                    'Jobs/Jobboard',
                    'Jobs/ApiJobListByChannel',
                    'Jobs/Template' => [ 'view', 'edittemplate' ],
                    'Jobs/Manage' => [
                        'template',
                    ],
                    'Jobs/ApiJobList',
                    'Jobs/ApiJobListByOrganization',
                    'route/lang/jobs/template',
                    'route/lang/jobboard',
                ],
                'deny' => 'JobList'
            ],
            'applicant' => [
                'allow' => [
                    'Jobboard'
                ],
            ],
            'admin' => [
                'allow' => [
                    'route/lang/jobs/approval',
                    'route/auth-logout',
                    'route/lang/my',
                    'Jobboard',
                    'route/lang/my-password',
                    'Jobs/Manage' => [
                        'approval',
                    ],
                    //'route/lang/jobs/listOpenJobs',
                    'pendingJobs',
                    'Entity/Jobs/Job' => ['delete'],
                ],
                'deny' => [
                    'lang/jobs',
                ]
            ]
        ],
        'assertions' => [
            'invokables' => [
                'Jobs/Write' => 'Jobs\Acl\WriteAssertion',
                'Jobs/Create' => 'Jobs\Acl\CreateAssertion',
            ],
        ],
    ],

    'navigation' => [
        'default' => [
            'admin' => [
                'pages' => [
                    'jobs' => [
                        'label' => /*@translate*/ 'Jobs',
                        'route' => 'lang/admin/jobs',
                        'query' => [
                            'clear' => '1'
                        ],
                        'active_on' => [
                            'lang/jobs/approval',
                        ]
                    ],
                    'jobs-categories' => [
                        'label' => /*@translate*/ 'Jobs categories',
                        'route' => 'lang/admin/jobs-categories',
                    ],
                ],
            ],
            'jobboard' => [
                'label' =>  /*@translate*/ 'Jobboard',
                'route' => 'lang/jobboard',
                'order' => '30',
                'resource' => 'Jobboard',
            ],
            'jobs' => [
                'label' =>  /*@translate*/ 'Jobs',
                'route' => 'lang/jobs',
                'order' => '30',
                'resource' => 'Jobs',
                'pages' => [
                    'list' => [
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/jobs',
                        'resource' => 'JobList'
                    ],
//                    'pending-list' => array(
//                        'label' => /*@translate*/ 'Pending jobs',
//                        'route' => 'lang/jobs/listOpenJobs',
//                        'resource' => 'pendingJobs'
//                    ),
                    'new' => [
                        'label' => /*@translate*/ 'Create job',
                        'route' => 'lang/jobs/manage',
                        'resource' => 'route/lang/jobs/manage',
                        'params' => [
                            'action' => 'edit'
                        ],
                        'id' => 'Jobs/new',
                    ],
                    'edit' => [
                        'label' => /*@translate*/ 'Edit job',
                        'resource' => 'route/lang/jobs/manage',
                        'uri' => '#',
                        'visible' => false,
                        'id' => 'Jobs/edit'
                    ],
//                    'jobboard-recruiter' => array(
//                        'label' =>  /*@translate*/ 'Jobboard',
//                        'route' => 'lang/jobboard',
//                        'order' => '30',
//                        'resource' => 'JobboardRecruiter',
//                    ),
                ],
            ],
        ],
    ],

    'service_manager' => [
        'invokables' => [
                'Jobs/Event'                        => 'Jobs\Listener\Events\JobEvent',
        ],
        'factories' => [
            'Jobs/Options'                                => 'Jobs\Factory\ModuleOptionsFactory',
            'Jobs/Options/Provider'                       => 'Jobs\Factory\Options\ProviderOptionsFactory',
            'Jobs/Options/Channel'                        => 'Jobs\Factory\Options\ChannelOptionsFactory',
            'Jobs\Form\Hydrator\OrganizationNameHydrator' => 'Jobs\Factory\Form\Hydrator\OrganizationNameHydratorFactory',
            'Jobs/JsonJobsEntityHydrator'                 => 'Jobs\Entity\Hydrator\JsonJobsEntityHydratorFactory',
            'Jobs/RestClient'                             => 'Jobs\Factory\Service\JobsPublisherFactory',
            //'Jobs/Events'                                 => 'Jobs\Factory\JobEventManagerFactory',
            'Jobs/Listener/MailSender'                    => 'Jobs\Factory\Listener\MailSenderFactory',
            'Jobs/Listener/AdminWidgetProvider'           => 'Jobs\Factory\Listener\AdminWidgetProviderFactory',
            'Jobs/ViewModelTemplateFilter'                => 'Jobs\Factory\Filter\ViewModelTemplateFilterFactory',
            'Jobs\Model\ApiJobDehydrator'                 => 'Jobs\Factory\Model\ApiJobDehydratorFactory',
            'Jobs/Listener/Publisher'                     => [Publisher::class,'factory'],
            'Jobs/PreviewLinkHydrator'                    => 'Jobs\Form\Hydrator\PreviewLinkHydrator::factory',
            'Jobs\Auth\Dependency\ListListener'           => 'Jobs\Factory\Auth\Dependency\ListListenerFactory',
            'Jobs/DefaultCategoriesBuilder'              => 'Jobs\Factory\Repository\DefaultCategoriesBuilderFactory',
            \Jobs\Listener\DeleteJob::class               => \Jobs\Factory\Listener\DeleteJobFactory::class,
            \Jobs\Listener\GetOrganizationManagers::class => \Jobs\Factory\Listener\GetOrganizationManagersFactory::class,
            \Jobs\Listener\LoadActiveOrganizations::class => \Jobs\Factory\Listener\LoadActiveOrganizationsFactory::class,

        ],
        'shared' => [
            'Jobs/Event' => false,
            'Jobs/Options/Channel' => false,
        ]
    ],


    'event_manager' => [
        'Core/AdminController/Events' => [ 'listeners' => [
            'Jobs/Listener/AdminWidgetProvider' => \Core\Controller\AdminControllerEvent::EVENT_DASHBOARD,
        ]],

        'Jobs/Events' => [
            'service' => 'Core/EventManager',
            'event' => '\Jobs\Listener\Events\JobEvent',
        ],

        'Jobs/JobContainer/Events' => [
            'event' => '\Core\Form\Event\FormEvent',
        ],
        'Auth/Dependency/Manager/Events' => [
            'listeners' => [
                'Jobs\Auth\Dependency\ListListener' => [
                    \Auth\Dependency\Manager::EVENT_GET_LISTS,
                    /* lazy */ true
                ]
            ]
        ],
        'Core/Ajax/Events' => ['listeners' => [
            \Jobs\Listener\DeleteJob::class => ['jobs.delete', true],
            \Jobs\Listener\GetOrganizationManagers::class => ['jobs.manager-select', true],
            \Jobs\Listener\LoadActiveOrganizations::class => [ 'jobs.admin.activeorganizations', true],

        ]],

        'Core/EntityEraser/Load/Events' => ['listeners' => [
            Listener\LoadExpiredJobsToPurge::class => [
                'events' => [
                    Listener\LoadExpiredJobsToPurge::EVENT_NAME,
                    \Core\Service\EntityEraser\LoadEvent::FETCH_LIST => 'onFetchList',
                ],
                'lazy' => true
            ],
        ]],
    ],


    'controllers' => [
        'invokables' => [
            'Jobs/ApiJobList' => 'Jobs\Controller\ApiJobListController',
            'Jobs/ApiJobListByChannel' => 'Jobs\Controller\ApiJobListByChannelController',
        ],
        'factories' => [
            'Jobs/Import' => [ Controller\ImportController::class, 'factory'],
        	'Jobs/Console' => [ConsoleController::class,'factory'],
	        'Jobs/AdminCategories' => [AdminCategoriesController::class,'factory'],
	        'Jobs/Admin'      => [AdminController::class,'factory'],
            'Jobs/Template' => 'Jobs\Factory\Controller\TemplateControllerFactory',
            'Jobs/Index' => 'Jobs\Factory\Controller\IndexControllerFactory',
            'Jobs/Approval' => 'Jobs\Factory\Controller\ApprovalControllerFactory',
            'Jobs/Jobboard' => 'Jobs\Factory\Controller\JobboardControllerFactory',
            'Jobs/AssignUser' => 'Jobs\Factory\Controller\AssignUserControllerFactory',
            'Jobs/ApiJobListByOrganization' => 'Jobs\Factory\Controller\ApiJobListByOrganizationControllerFactory',
            'Jobs/Manage' => 'Jobs\Factory\Controller\ManageControllerFactory',
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            'initializeJob' => 'Jobs\Factory\Controller\Plugin\InitializeJobFactory',
        ],
    ],

    'paginator_manager' => [
        'invokables' => [
        ],
        'factories' => [
            'Jobs/Job'   => 'Jobs\Paginator\JobsPaginatorFactory',
            'Jobs/Admin' => 'Jobs\Paginator\JobsAdminPaginatorFactory',
            'Jobs\Paginator\ActiveOrganizations' => \Jobs\Factory\Paginator\ActiveOrganizationsPaginatorFactory::class,
        ],
        'aliases' => [
            'Jobs/Board' => 'Jobs/Job'
        ]
    ],

    'view_manager' => [
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => [
            'jobs/form/list-filter' => __DIR__ . '/../view/form/list-filter.phtml',
            'jobs/form/apply-identifier' => __DIR__ . '/../view/form/apply-identifier.phtml',
            'jobs/form/hiring-organization-select' => __DIR__ . '/../view/form/hiring-organization-select.phtml',
            'jobs/form/multiposting-select' => __DIR__ . '/../view/form/multiposting-select.phtml',
            'jobs/form/multiposting-checkboxes' => __DIR__ . '/../view/form/multiposting-checkboxes.phtml',
            'jobs/form/ats-mode.view' => __DIR__ . '/../view/form/ats-mode.view.phtml',
            'jobs/form/ats-mode.form' => __DIR__ . '/../view/form/ats-mode.form.phtml',
            'jobs/form/company-name-fieldset' => __DIR__ . '/../view/form/company-name-fieldset.phtml',
            'jobs/form/preview' => __DIR__ . '/../view/form/preview.phtml',
            'jobs/form/customer-note' => __DIR__ . '/../view/form/customer-note.phtml',
            'jobs/partials/channel-list' => __DIR__ . '/../view/partials/channel-list.phtml',
            'jobs/assign-user' => __DIR__ . '/../view/jobs/manage/assign-user.phtml',
            'jobs/snapshot_or_preview' => __DIR__ . '/../view/partials/snapshot_or_preview.phtml',
            'jobs/history' => __DIR__ . '/../view/partials/history.phtml',
            'jobs/portalsummary' => __DIR__ . '/../view/partials/portalsummary.phtml',
            'content/jobs-publish-on-yawik' => __DIR__ . '/../view/modals/yawik.phtml',
            'content/jobs-publish-on-jobsintown' => __DIR__ . '/../view/modals/jobsintown.phtml',
            'content/jobs-publish-on-homepage' => __DIR__ . '/../view/modals/homepage.phtml',
            'content/jobs-publish-on-fazjob' => __DIR__ . '/../view/modals/fazjob.phtml',
            'content/jobs-terms-and-conditions' => __DIR__ . '/../view/jobs/index/terms.phtml',
            'mail/job-created' => __DIR__ . '/../view/mails/job-created.phtml',
            'mail/job-pending' => __DIR__ . '/../view/mails/job-pending.phtml',
            'mail/job-accepted' => __DIR__ . '/../view/mails/job-accepted.phtml',
            'mail/job-rejected' => __DIR__ . '/../view/mails/job-rejected.phtml',
            'mail/job-created.en' => __DIR__ . '/../view/mails/job-created.en.phtml',
            'mail/job-pending.en' => __DIR__ . '/../view/mails/job-pending.en.phtml',
            'mail/job-accepted.en' => __DIR__ . '/../view/mails/job-accepted.en.phtml',
            'mail/job-rejected.en' => __DIR__ . '/../view/mails/job-rejected.en.phtml',
            'jobs/error/no-parent' => __DIR__ . '/../view/error/no-parent.phtml',
            'jobs/error/expired' => __DIR__ . '/../view/error/expired.phtml',
        ],

        // Where to look for view templates not mapped above
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'jobPreviewLink' => 'Jobs\Form\View\Helper\PreviewLink',
            'jobApplyButtons' => 'Jobs\View\Helper\ApplyButtons'

        ],
        'factories' => [
            'applyUrl' => 'Jobs\Factory\View\Helper\ApplyUrlFactory',
            'jobUrl' => 'Jobs\Factory\View\Helper\JobUrlFactory',
            'Jobs/AdminEditLink' => 'Jobs\Factory\View\Helper\AdminEditLinkFactory',
            View\Helper\JsonLd::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            View\Helper\JobOrganizationName::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'aliases' => [
            'jsonLd' => View\Helper\JsonLd::class,
            'jobOrganizationName' => View\Helper\JobOrganizationName::class,
        ],

    ],

    'form_elements' => [
        'invokables' => [
            'Jobs/Base'                         => 'Jobs\Form\Base',
            'Jobs/Employers'                    => 'Jobs\Form\JobEmployers',
            'Jobs/JobEmployersFieldset'         => 'Jobs\Form\JobEmployersFieldset',
            'Jobs/Description'                  => 'Jobs\Form\JobDescription',
            'Jobs/JobDescriptionFieldset'       => 'Jobs\Form\JobDescriptionFieldset',
            'Jobs/ApplyId'                      => 'Jobs\Form\ApplyIdentifierElement',
            'Jobs/ImportFieldset'               => 'Jobs\Form\ImportFieldset',
            'Jobs/ListFilterPersonalFieldset'   => 'Jobs\Form\ListFilterPersonalFieldset',
            'Jobs/ListFilterAdminFieldset'      => 'Jobs\Form\ListFilterAdminFieldset',
            'Jobs/JobDescriptionDescription'    => 'Jobs\Form\JobDescriptionDescription',
            'Jobs/JobDescriptionBenefits'       => 'Jobs\Form\JobDescriptionBenefits',
            'Jobs/JobDescriptionRequirements'   => 'Jobs\Form\JobDescriptionRequirements',
            'Jobs/TemplateLabelRequirements'    => 'Jobs\Form\TemplateLabelRequirements',
            'Jobs/TemplateLabelQualifications'  => 'Jobs\Form\TemplateLabelQualifications',
            'Jobs/TemplateLabelBenefits'        => 'Jobs\Form\TemplateLabelBenefits',
            'Jobs/JobDescriptionQualifications' => 'Jobs\Form\JobDescriptionQualifications',
            'Jobs/JobDescriptionTitle'          => 'Jobs\Form\JobDescriptionTitle',
            'Jobs/JobDescriptionHtml'           => 'Jobs\Form\JobDescriptionHtml',
            'Jobs/Description/Template'         => 'Jobs\Form\JobDescriptionTemplate',
            'Jobs/Preview'                      => 'Jobs\Form\Preview',
            'Jobs/PreviewFieldset'              => 'Jobs\Form\PreviewFieldset',
            'Jobs/PreviewLink'                  => 'Jobs\Form\PreviewLink',
            'Jobs/CompanyName'                  => 'Jobs\Form\CompanyName',
            'Jobs/CompanyNameElement'           => 'Jobs\Form\CompanyNameElement',
            'Jobs/Multipost'                    => 'Jobs\Form\Multipost',
            'Jobs/MultipostFieldset'            => 'Jobs\Form\MultipostFieldset',
            'Jobs/MultipostButtonFieldset'      => 'Jobs\Form\MultipostButtonFieldset',
            'Jobs/AtsMode'                      => 'Jobs\Form\AtsMode',
            'Jobs/AtsModeFieldset'              => 'Jobs\Form\AtsModeFieldset',
            'Jobs/AdminSearch'                  => 'Jobs\Form\AdminSearchFormElementsFieldset',
            'Jobs/ListFilter'                   => 'Jobs\Form\ListFilter',
            'Jobs/ListFilterLocation'           => 'Jobs\Form\ListFilterLocation',
            'Jobs/ListFilterPersonal'           => 'Jobs\Form\ListFilterPersonal',
            'Jobs/ListFilterAdmin'              => 'Jobs\Form\ListFilterAdmin',
            'Jobs/StatusSelect'                 => 'Jobs\Form\Element\StatusSelect',
            'Jobs/AdminJobEdit'                 => 'Jobs\Form\AdminJobEdit',
            'Jobs/AdminCategories'              => 'Jobs\Form\CategoriesContainer',
            'Jobs/Classifications'              => 'Jobs\Form\ClassificationsForm',
            'Jobs/ClassificationsFieldset'      => 'Jobs\Form\ClassificationsFieldset',
            'Jobs/CustomerNote'                 => 'Jobs\Form\CustomerNote',
            'Jobs/CustomerNoteFieldset'         => 'Jobs\Form\CustomerNoteFieldset',
            'Jobs/ManagerSelect'                => 'Jobs\Form\Element\ManagerSelect',

        ],
        'factories' => [
            'Jobs/Job'                          => 'Jobs\Factory\Form\JobFactory',
            'Jobs/BaseFieldset'                 => 'Jobs\Factory\Form\BaseFieldsetFactory',
            'Jobs/ListFilterLocationFieldset'   => 'Jobs\Factory\Form\ListFilterLocationFieldsetFactory',
            'Jobs/JobboardSearch'               => 'Jobs\Factory\Form\JobboardSearchFactory',
            'Jobs/CompanyNameFieldset'          => 'Jobs\Factory\Form\CompanyNameFieldsetFactory',
            'Jobs/HiringOrganizationSelect'     => 'Jobs\Factory\Form\HiringOrganizationSelectFactory',
            'Jobs/ActiveOrganizationSelect'     => 'Jobs\Factory\Form\ActiveOrganizationSelectFactory',
            'Jobs/MultipostingSelect'           => 'Jobs\Factory\Form\MultipostingMultiCheckboxFactory',
            'Jobs/Import'                       => 'Jobs\Factory\Form\ImportFactory',
        ],
    ],

    'input_filters' => [
        'invokables' => [
            'Jobs/Location/New'                 => 'Jobs\Form\InputFilter\JobLocationNew',
            //'Jobs/Location/Edit'                => 'Jobs\Form\InputFilter\JobLocationEdit',
	        JobLocationEdit::class => JobLocationEdit::class,
            'Jobs/Company'                      => 'Jobs\Form\InputFilter\CompanyName',
        ],
        'factories' => [
            'Jobs/AtsMode'                      => 'Jobs\Factory\Form\InputFilter\AtsModeFactory',
        ],
	    'aliases' => [
	    	'Jobs/Location/Edit' => JobLocationEdit::class
	    ]
    ],

    'filters' => [
        'invokables' => [
            'Jobs/PaginationAdminQuery' => 'Jobs\Repository\Filter\PaginationAdminQuery',
        ],
        'factories'=> [
            'Jobs/PaginationQuery'      => 'Jobs\Factory\Repository\Filter\PaginationQueryFactory',
            'Jobs/ChannelPrices'        => 'Jobs\Factory\Filter\ChannelPricesFactory',
        ],
    ],

    'validators' => [
        'factories' => [
            'Jobs/Form/UniqueApplyId' => 'Jobs\Form\Validator\UniqueApplyIdFactory',
        ],
    ],





];
