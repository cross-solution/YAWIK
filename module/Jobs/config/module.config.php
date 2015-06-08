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
                    'Jobs/Manage' => array(
                        'edit',
                        'completion',
                        'template',
                        'new' => 'Jobs/Create',
                    ),
                    'JobboardRecruiter',
                    'route/lang/jobs/manage',
                    'route/lang/jobs/template',
                    'Entity/Jobs/Job' => array(
                        'edit' => 'Jobs/Write',
                    ),
                ),
                'deny' => array(
                    'Jobboard',
                    'route/lang/jobs/approval'
                ),
            ),
            'guest' => array(
                'allow' => array(
                    'Jobboard'
                ),
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
                    'route/lang/jobs/pending-list',
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
                        'params' => array('__activeMarker__' => 'overview'),
                    ),
                    'pending-list' => array(
                        'label' => /*@translate*/ 'Pending jobs',
                        'route' => 'lang/jobs',
                        'query' => array('status' => 'created'),
                        'resource' => 'route/lang/jobs/pending-list',
                        'params' => array('__activeMarker__' => 'pending'),
                    ),
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
            'Jobs/Events'                                 => 'Jobs\Factory\JobEventManagerFactory',
            'Jobs/Listener/MailSender'                    => 'Jobs\Factory\Listener\MailSenderFactory',
            'Jobs/viewModelTemplateFilter'                => 'Jobs\Filter\viewModelTemplateFilterFactory'
        ),
        'shared' => array(
            'Jobs/Event' => false,
            'Jobs/Options/Channel' => false,
        )
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Jobs/Index' => 'Jobs\Controller\IndexController',
            'Jobs/Manage' => 'Jobs\Controller\ManageController',
            'Jobs/Import' => 'Jobs\Controller\ImportController',
            'Jobs/Console' => 'Jobs\Controller\ConsoleController'
        ),
        'factories' => array(
            'Jobs/Template' => 'Jobs\Factory\Controller\TemplateControllerFactory',
            'Jobs/Jobboard' => 'Jobs\Factory\Controller\JobboardControllerFactory',
            'Jobs/AssignUser' => 'Jobs\Factory\Controller\AssignUserControllerFactory',
        )
    ),
    
    'view_manager' => array(
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'jobs/form/list-filter' => __DIR__ . '/../view/form/list-filter.phtml',
            'jobs/form/apply-identifier' => __DIR__ . '/../view/form/apply-identifier.phtml',
            'jobs/form/hiring-organization-select' => __DIR__ . '/../view/form/hiring-organization-select.phtml',
            'jobs/form/multiposting-select' => __DIR__ . '/../view/form/multiposting-select.phtml',
            'jobs/form/ats-mode.view' => __DIR__ . '/../view/form/ats-mode.view.phtml',
            'jobs/form/ats-mode.form' => __DIR__ . '/../view/form/ats-mode.form.phtml',
            'jobs/assign-user' => __DIR__ . '/../view/jobs/manage/assign-user.phtml',
            'content/jobs-publish-on-yawik' => __DIR__ . '/../view/modals/yawik.phtml',
            'content/jobs-publish-on-jobsintown' => __DIR__ . '/../view/modals/jobsintown.phtml',
            'content/jobs-publish-on-homepage' => __DIR__ . '/../view/modals/homepage.phtml',
            'content/jobs-terms-and-conditions' => __DIR__ . '/../view/jobs/index/terms.phtml',
            'mail/job-created' => __DIR__ . '/../view/mails/job-created.phtml',
            'mail/job-pending' => __DIR__ . '/../view/mails/job-pending.phtml',
            'mail/job-accepted' => __DIR__ . '/../view/mails/job-accepted.phtml',
            'mail/job-rejected' => __DIR__ . '/../view/mails/job-rejected.phtml',
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
            'Jobs/Job'                          => 'Jobs\Form\Job',
            'Jobs/Base'                         => 'Jobs\Form\Base',
            'Jobs/BaseFieldset'                 => 'Jobs\Form\BaseFieldset',
            'Jobs/Employers'                    => 'Jobs\Form\JobEmployers',
            'Jobs/JobEmployersFieldset'         => 'Jobs\Form\JobEmployersFieldset',
            'Jobs/Description'                  => 'Jobs\Form\JobDescription',
            'Jobs/JobDescriptionFieldset'       => 'Jobs\Form\JobDescriptionFieldset',
            'Jobs/ApplyId'                      => 'Jobs\Form\ApplyIdentifierElement',
            'Jobs/Import'                       => 'Jobs\Form\Import',
            'Jobs/ImportFieldset'               => 'Jobs\Form\ImportFieldset',
            'Jobs/ListFilter'                   => 'Jobs\Form\ListFilter',
            'Jobs/ListFilterFieldset'           => 'Jobs\Form\ListFilterFieldset',
            'Jobs/JobDescriptionDescription'    => 'Jobs\Form\JobDescriptionDescription',
            'Jobs/JobDescriptionBenefits'       => 'Jobs\Form\JobDescriptionBenefits',
            'Jobs/JobDescriptionRequirements'   => 'Jobs\Form\JobDescriptionRequirements',
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
            'Jobs/AtsMode'                      => 'Jobs\Form\AtsMode',
            'Jobs/AtsModeFieldset'              => 'Jobs\Form\AtsModeFieldset',
        ),
        'factories' => array(
            'Jobs/ListFilterFieldsetExtended'   => 'Jobs\Factory\Form\ListFilterFieldsetExtendedFactory',
            'Jobs/CompanyNameFieldset'          => 'Jobs\Factory\Form\CompanyNameFieldsetFactory',
            'Jobs/HiringOrganizationSelect'     => 'Jobs\Factory\Form\HiringOrganizationSelectFactory',
            'Jobs/MultipostingSelect'           => 'Jobs\Factory\Form\MultipostingSelectFactory',
        )
    ),
    
    'input_filters' => array(
        'invokables' => array(
            'Jobs/Location/New'                 => 'Jobs\Form\InputFilter\JobLocationNew',
            'Jobs/Location/Edit'                => 'Jobs\Form\InputFilter\JobLocationEdit',
            'Jobs/Company'                      => 'Jobs\Form\InputFilter\CompanyName',
            'Jobs/AtsMode'                      => 'Jobs\Form\InputFilter\AtsMode',
        ),
    ),
    
    'filters' => array(
        'factories'=> array(
            'Jobs/PaginationQuery' => '\Jobs\Repository\Filter\PaginationQueryFactory'
        ),
    ),
    
    'validators' => array(
        'factories' => array(
            'Jobs/Form/UniqueApplyId' => 'Jobs\Form\Validator\UniqueApplyIdFactory',
        ),
    ),





);