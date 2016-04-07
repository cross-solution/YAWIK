<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Jobs\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => array( __DIR__ . '/../src/Jobs/Entity'),
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    '\Jobs\Repository\Event\UpdatePermissionsSubscriber',
                ),
            ),
        ),
    ),

    'Jobs' => array(
        'dashboard' => array(
            'enabled' => true,
            'widgets' => array(
                'recentJobs' => array(
                    'controller' => 'Jobs/Index',
                    'params' => array(
                        'type' => 'recent'
                    ),
                ),
            ),
        ),
    ),

    // Translations
    'translator' => array(
            'translation_file_patterns' => array(
                    array(
                            'type'     => 'gettext',
                            'base_dir' => __DIR__ . '/../language',
                            'pattern'  => '%s.mo',
                    ),
            ),
    ),

    'acl' => array(
        'rules' => array(
            'recruiter' => array(
                'allow' => array(
                    'Jobs',
                    'JobList',
                    'Jobs/Manage' => [
                        'edit',
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
                    'Entity/Jobs/Job' => array(
                        'edit' => 'Jobs/Write',
                    ),
                ),
                'deny' => array(
                    'Jobboard',
                    'route/lang/jobs/approval',
                ),
            ),
            'guest' => array(
                'allow' => array(
                    'Jobboard',
                    'Jobs/Manage' => array(
                        'template',
                    ),
                    'route/lang/jobs/template',
                ),
                'deny' => 'JobList'
            ),
            'applicant' => array(
                'allow' => array(
                    'Jobboard'
                ),
            ),
            'admin' => array(
                'allow' => array(
                    'route/lang/jobs/approval',
                    'route/auth-logout',
                    'route/lang/my',
                    'Jobboard',
                    'route/lang/my-password',
                    'Jobs/Manage' => array(
                        'approval',
                    ),
                    //'route/lang/jobs/listOpenJobs',
                    'pendingJobs',
                ),
                'deny' => array(
                    'lang/jobs',
                )
            )
        ),
        'assertions' => array(
            'invokables' => array(
                'Jobs/Write' => 'Jobs\Acl\WriteAssertion',
                'Jobs/Create' => 'Jobs\Acl\CreateAssertion',
            ),
        ),
    ),

    'navigation' => array(
        'default' => array(
            'admin' => [
                'pages' => [
                    'jobs' => [
                        'label' => /*@translate*/ 'Jobs',
                        'route' => 'lang/admin/jobs',
                        'query' => array(
                            'clear' => '1'
                        ),
                    ],
                ],
            ],
            'jobboard' => array(
                'label' =>  /*@translate*/ 'Jobboard',
                'route' => 'lang/jobboard',
                'order' => '30',
                'resource' => 'Jobboard',
            ),
            'jobs' => array(
                'label' =>  /*@translate*/ 'Jobs',
                'route' => 'lang/jobs',
                'order' => '30',
                'resource' => 'Jobs',
                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/jobs',
                        'resource' => 'JobList'
                    ),
//                    'pending-list' => array(
//                        'label' => /*@translate*/ 'Pending jobs',
//                        'route' => 'lang/jobs/listOpenJobs',
//                        'resource' => 'pendingJobs'
//                    ),
                    'new' => array(
                        'label' => /*@translate*/ 'Create job',
                        'route' => 'lang/jobs/manage',
                        'resource' => 'route/lang/jobs/manage',
                        'params' => array(
                            'action' => 'edit'
                        ),
                        'id' => 'Jobs/new',
                    ),
                    'edit' => array(
                        'label' => /*@translate*/ 'Edit job',
                        'resource' => 'route/lang/jobs/manage',
                        'uri' => '#',
                        'visible' => false,
                        'id' => 'Jobs/edit'
                    ),
                    'jobboard-recruiter' => array(
                        'label' =>  /*@translate*/ 'Jobboard',
                        'route' => 'lang/jobboard',
                        'order' => '30',
                        'resource' => 'JobboardRecruiter',
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
                'Jobs/PreviewLinkHydrator'          => 'Jobs\Form\Hydrator\PreviewLinkHydrator',
                'Jobs/Event'                        => 'Jobs\Listener\Events\JobEvent',
                'Jobs/Listener/Publisher'           => 'Jobs\Listener\Publisher',
        ),
        'factories' => array(
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

        ),
        'shared' => array(
            'Jobs/Event' => false,
            'Jobs/Options/Channel' => false,
        )
    ),

    'event_manager' => [
        'Core/AdminController/Events' => [ 'listeners' => [
            'Jobs/Listener/AdminWidgetProvider' => \Core\Controller\AdminControllerEvent::EVENT_DASHBOARD,
        ]],

        'Jobs/Events' => [
            'event' => '\Jobs\Listener\Events\JobEvent',
        ],

        'Jobs/JobContainer/Events' => [
            'event' => '\Core\Form\Event\FormEvent',
        ],
    ],

    'controllers' => array(
        'invokables' => array(
            'Jobs/Import' => 'Jobs\Controller\ImportController',
            'Jobs/Console' => 'Jobs\Controller\ConsoleController',
            'Jobs/ApiJobList' => 'Jobs\Controller\ApiJobListController',
            'Jobs/Admin'      => 'Jobs\Controller\AdminController',

        ),
        'factories' => array(
            'Jobs/Template' => 'Jobs\Factory\Controller\TemplateControllerFactory',
            'Jobs/Index' => 'Jobs\Factory\Controller\IndexControllerFactory',
            'Jobs/Approval' => 'Jobs\Factory\Controller\ApprovalControllerFactory',
            'Jobs/Jobboard' => 'Jobs\Factory\Controller\JobboardControllerFactory',
            'Jobs/AssignUser' => 'Jobs\Factory\Controller\AssignUserControllerFactory',
            'Jobs/ApiJobListByOrganization' => 'Jobs\Factory\Controller\ApiJobListByOrganizationControllerFactory',
            'Jobs/Manage' => 'Jobs\Factory\Controller\ManageControllerFactory',
        )
    ),

    'controller_plugins' => [
        'factories' => [
            'initializeJob' => 'Jobs\Factory\Controller\Plugin\InitializeJobFactory',
        ],
    ],

    'paginator_manager' => array(
        'invokables' => array(
        ),
        'factories' => array(
            'Jobs/Job'   => 'Jobs\Paginator\JobsPaginatorFactory',
            'Jobs/Admin' => 'Jobs\Paginator\JobsAdminPaginatorFactory',
        ),
        'aliases' => array(
            'Jobs/Board' => 'Jobs/Job'
        )
    ),

    'view_manager' => array(
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'jobs/form/list-filter' => __DIR__ . '/../view/form/list-filter.phtml',
            'jobs/form/apply-identifier' => __DIR__ . '/../view/form/apply-identifier.phtml',
            'jobs/form/hiring-organization-select' => __DIR__ . '/../view/form/hiring-organization-select.phtml',
            'jobs/form/multiposting-select' => __DIR__ . '/../view/form/multiposting-select.phtml',
            'jobs/form/multiposting-checkboxes' => __DIR__ . '/../view/form/multiposting-checkboxes.phtml',
            'jobs/form/ats-mode.view' => __DIR__ . '/../view/form/ats-mode.view.phtml',
            'jobs/form/ats-mode.form' => __DIR__ . '/../view/form/ats-mode.form.phtml',
            'jobs/form/preview' => __DIR__ . '/../view/form/preview.phtml',
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
        ),

        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'jobPreviewLink' => 'Jobs\Form\View\Helper\PreviewLink',

        ),
        'factories' => array(
            'applyUrl' => 'Jobs\Factory\View\Helper\ApplyUrlFactory',
        ),

    ),

    'form_elements' => array(
        'invokables' => array(
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
            'Jobs/AdminSearch'                  => 'Jobs\Form\AdminSearchForm',
            'Jobs/ListFilter'                   => 'Jobs\Form\ListFilter',
            'Jobs/ListFilterLocation'           => 'Jobs\Form\ListFilterLocation',
            'Jobs/ListFilterPersonal'           => 'Jobs\Form\ListFilterPersonal',
            'Jobs/ListFilterAdmin'              => 'Jobs\Form\ListFilterAdmin',
            'Jobs/StatusSelect'                 => 'Jobs\Form\Element\StatusSelect',
            'Jobs/AdminJobEdit'                 => 'Jobs\Form\AdminJobEdit',

        ),
        'factories' => array(
            'Jobs/Job'                          => 'Jobs\Factory\Form\JobFactory',
            'Jobs/BaseFieldset'                 => 'Jobs\Factory\Form\BaseFieldsetFactory',
            'Jobs/ListFilterLocationFieldset'   => 'Jobs\Factory\Form\ListFilterLocationFieldsetFactory',
            'Jobs/CompanyNameFieldset'          => 'Jobs\Factory\Form\CompanyNameFieldsetFactory',
            'Jobs/HiringOrganizationSelect'     => 'Jobs\Factory\Form\HiringOrganizationSelectFactory',
            'Jobs/ActiveOrganizationSelect'     => 'Jobs\Factory\Form\ActiveOrganizationSelectFactory',
            'Jobs/MultipostingSelect'           => 'Jobs\Factory\Form\MultipostingMultiCheckboxFactory',
            'Jobs/Import'                       => 'Jobs\Factory\Form\ImportFactory',
        )
    ),

    'input_filters' => array(
        'invokables' => array(
            'Jobs/Location/New'                 => 'Jobs\Form\InputFilter\JobLocationNew',
            'Jobs/Location/Edit'                => 'Jobs\Form\InputFilter\JobLocationEdit',
            'Jobs/Company'                      => 'Jobs\Form\InputFilter\CompanyName',
        ),
        'factories' => array(
            'Jobs/AtsMode'                      => 'Jobs\Factory\Form\InputFilter\AtsModeFactory',
        )
    ),

    'filters' => array(
        'invokables' => [
            'Jobs/PaginationAdminQuery' => 'Jobs\Repository\Filter\PaginationAdminQuery',
        ],
        'factories'=> array(
            'Jobs/PaginationQuery'      => 'Jobs\Factory\Repository\Filter\PaginationQueryFactory',
            'Jobs/ChannelPrices'        => 'Jobs\Factory\Filter\ChannelPricesFactory',
        ),
    ),

    'validators' => array(
        'factories' => array(
            'Jobs/Form/UniqueApplyId' => 'Jobs\Form\Validator\UniqueApplyIdFactory',
        ),
    ),





);
