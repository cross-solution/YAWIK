<?php
/**
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */

return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Organizations\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                'paths' => [ __DIR__ . '/../src/Entity']
            ],
        ],
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    '\Organizations\Repository\Event\InjectOrganizationReferenceListener',
                    'Organizations\ImageFileCache\ODMListener'
                ],
            ],
        ],
    ],

    'Organizations' => [
        'form' => [
        ],
        'dashboard' => [
            'enabled' => false,
            'widgets' => [
            ],
        ],
    ],
    // Translations
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],


    'controllers' => [
        'factories' => [
            'Organizations/InviteEmployee' => \Organizations\Factory\Controller\InviteEmployeeControllerFactory::class,
            'Organizations/Index' => 'Organizations\Factory\Controller\IndexControllerFactory',
            'Organizations/Profile' => 'Organizations\Factory\Controller\ProfileControllerFactory'
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            'Organizations/InvitationHandler' => 'Organizations\Factory\Controller\Plugin\InvitationHandlerFactory',
            'Organizations/AcceptInvitationHandler' => 'Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory',
            'Organizations/GetOrganizationHandler' => 'Organizations\Factory\Controller\Plugin\GetOrganizationHandlerFactory',
        ],
    ],

    'view_manager' => [
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => [
             'organizations/index/edit' => __DIR__ . '/../view/organizations/index/form.phtml',
             'organizations/form/employees-fieldset' => __DIR__ . '/../view/form/employees-fieldset.phtml',
             'organizations/form/employee-fieldset' => __DIR__ .'/../view/form/employee-fieldset.phtml',
             'organizations/form/invite-employee-bar' => __DIR__ . '/../view/form/invite-employee-bar.phtml',
             'organizations/error/no-parent' => __DIR__ . '/../view/error/no-parent.phtml',
             'organizations/error/invite' => __DIR__ . '/../view/error/invite.phtml',
             'organizations/mail/invite-employee' => __DIR__ . '/../view/mail/invite-employee.phtml',
             'organizations/form/workflow-fieldset' => __DIR__ . '/../view/form/workflow-fieldset.phtml',
            'organizations/profile/disabled' => __DIR__ . '/../view/organizations/profile/disabled.phtml',
        ],
        // Where to look for view templates not mapped above
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'form_elements' => [
        'invokables' => [
             'Organizations/Form' => 'Organizations\Form\Organizations',
             'Organizations/OrganizationsContactForm'     => 'Organizations\Form\OrganizationsContactForm',
             'Organizations/OrganizationsNameForm'        => 'Organizations\Form\OrganizationsNameForm',
             'Organizations/OrganizationsDescriptionForm' => 'Organizations\Form\OrganizationsDescriptionForm',
             'Organizations/OrganizationsContactFieldset' => 'Organizations\Form\OrganizationsContactFieldset',
             'Organizations/OrganizationsDescriptionFieldset' => 'Organizations\Form\OrganizationsDescriptionFieldset',
             //'Organizations/OrganizationFieldset'       => 'Organizations\Form\OrganizationFieldset',
             'Organizations/EmployeesContainer'           => 'Organizations\Form\EmployeesContainer',
             'Organizations/Employees'                    => 'Organizations\Form\Employees',
             'Organizations/InviteEmployeeBar'            => 'Organizations\Form\Element\InviteEmployeeBar',
             'Organizations/Employee'                     => 'Organizations\Form\Element\Employee',
             'Organizations/WorkflowSettings'             => 'Organizations\Form\WorkflowSettings',
             'Organizations/WorkflowSettingsFieldset'     => 'Organizations\Form\WorkflowSettingsFieldset',
             'Organizations/Profile'                      => \Organizations\Form\OrganizationsProfileForm::class,
             'Organizations/ProfileFieldset'              => \Organizations\Form\OrganizationsProfileFieldset::class
        ],
        'factories' => [
            'Organizations/OrganizationsNameFieldset'    => \Organizations\Factory\Form\OrganizationsNameFieldsetFactory::class,
            'Organizations/Image'                        => \Organizations\Form\LogoImageFactory::class,
            'Organizations/EmployeesFieldset'            => 'Organizations\Factory\Form\EmployeesFieldsetFactory',
            'Organizations/EmployeeFieldset'             => 'Organizations\Factory\Form\EmployeeFieldsetFactory',
        ]
    ],

    'form_elements_config' => [
        'file_upload_factories' => [
            'organization_logo_image' => [
                'hydrator' => 'Organizations/Logo',
            ],
        ],
    ],

    'input_filters' => [
        'invokables' => [
        ],
    ],

    'filters' => [
        'factories' => [
            'Organizations/PaginationQuery' => '\Organizations\Repository\Filter\PaginationQueryFactory',
            \Organizations\Repository\Filter\ListJobQuery::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'aliases' => [
            'PaginationQuery/Organizations/Organization' => 'Organizations/PaginationQuery',
            'Organizations/ListJobQuery' => \Organizations\Repository\Filter\ListJobQuery::class
        ]
    ],

    'validators' => [
        'factories' => [
        ],
    ],

    'hydrators' => [
        'factories' => [
            'Hydrator\Organization' => 'Organizations\Entity\Hydrator\OrganizationHydratorFactory',
            'Organizations/Logo' => \Organizations\Factory\Entity\Hydrator\LogoHydratorFactory::class,
        ],
    ],

    'mails' => [
        'factories' => [
            'Organizations/InviteEmployee' => 'Organizations\Mail\EmployeeInvitationFactory',
        ],
    ],

    'acl' => [
        'rules' => [
            // guests are not allowed to see a list of companies
            'guest' => [
                'allow' => [
                    'Entity/OrganizationImage',
                    'route/lang/organizations/invite',
                    'Organizations/InviteEmployee' => [ 'accept' ],
                    'route/lang/organizations/profile',
                    'route/lang/organizations/profileDetail',
                ],
                'deny' => [
                    'route/lang/organizations',
                    'Organizations/InviteEmployee' => [ 'invite' ],
                ],
            ],
            // recruiters are allowed to view their companies
            'recruiter' => [
                'allow' => [
                    'route/lang/organizations',
                    'Organizations/InviteEmployee',
                    'Entity/Organization' => [ 'edit' => 'Organizations/Write' ],
                    'route/lang/organizations/profile',
                    'route/lang/organizations/profileDetail'
                ],
            ],
        ],
        'assertions' => [
            'invokables' => [
                'Organizations/Write' => 'Organizations\Acl\Assertion\WriteAssertion',
            ],
        ],
    ],

    'navigation' => [
        'default' => [
            'organizations' => [
                'label' => 'Organizations',
                'route' => 'lang/organizations',
                'order' => 65,                             // allows to order the menu items
                'resource' => 'route/lang/organizations',  // if a resource is defined, the acl will be applied.

                'pages' => [
                    'list' => [
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/organizations',
                    ],
                    'profile' => [
                        'label' => /*@translate*/ 'Profiles',
                        'route' => 'lang/organizations/profile',
                    ],
                    'edit' => [
                        'label' => /*@translate*/ 'Insert',
                        'route' => 'lang/organizations/edit',
                    ],
                ],
            ],

        ],
    ],

    'service_manager' => [
        'invokables' => [
           'Organizations\Auth\Dependency\EmployeeListListener' => 'Organizations\Auth\Dependency\EmployeeListListener'
        ],
        'factories' => [
           'Organizations\Auth\Dependency\ListListener' => 'Organizations\Factory\Auth\Dependency\ListListenerFactory',
           'Organizations\ImageFileCache\Manager' => 'Organizations\Factory\ImageFileCache\ManagerFactory',
           'Organizations\ImageFileCache\ODMListener' => 'Organizations\Factory\ImageFileCache\ODMListenerFactory',
           'Organizations\ImageFileCache\ApplicationListener' => 'Organizations\Factory\ImageFileCache\ApplicationListenerFactory',
        ],
    ],

    'event_manager' => [
        'Auth/Dependency/Manager/Events' => [
            'listeners' => [
                'Organizations\Auth\Dependency\ListListener' => [
                    \Auth\Dependency\Manager::EVENT_GET_LISTS,
                    /* lazy */ true,
                    /* priority */ 10
                ],
                'Organizations\Auth\Dependency\EmployeeListListener' => [
                    \Auth\Dependency\Manager::EVENT_GET_LISTS,
                    /* lazy */ true,
                    /* priority */ 11
                ]
            ]
        ],
    ],

    \Core\Listener\DeleteImageSetListener::class => [
        Organizations\Entity\OrganizationImage::class => [
            'repository' => 'Organizations',
            'property'   => 'images',
        ],
    ],

    'options' => [
        'Organizations/ImageFileCacheOptions' => [
            'class' => '\Organizations\Options\ImageFileCacheOptions'
        ],
        \Organizations\Options\OrganizationLogoOptions::class => [],
    ],

    'paginator_manager' => [
        'factories' => [
            'Organizations/ListJob' => \Organizations\Paginator\ListJobPaginatorFactory::class
        ]
    ],
];
