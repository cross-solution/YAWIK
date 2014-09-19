<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Organizations\Entity' => 'annotation',
                ),
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                ),
            ),
        ),
    ),
    'Organizations' => array(
        'form' => array(
        ),
        'dashboard' => array(
            'enabled' => false,
            'widgets' => array(
            ),
        ),
    ),
    // Translations
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'acl' => array(
        'rules' => array(
            'guest' => array(
                'allow' => array(
//                    'route/lang/organizations',
                ),
                'deny' => array(
                    'route/lang/organizations',
                ),
            ),
            'recruiter' => array(
                'allow' => array(
                ),
            ),
        ),
        'assertions' => array(
            'invokables' => array(
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Organizations/Index' => 'Organizations\Controller\IndexController', 
        ),
    ),
    'view_manager' => array(
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
             'organizations/index/edit' => __DIR__ . '/../view/organizations/index/form.phtml',
        ),
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
             'organizations/form' => 'Organizations\Form\Organizations',
             'Organizations/ContactFieldset' => 'Organizations\Form\ContactFieldset',
             'Organizations/OrganizationFieldset' => 'Organizations\Form\OrganizationsFieldset',
        ),
        'factories' => array(
        )
    ),
    'input_filters' => array(
        'invokables' => array(
        ),
    ),
    'filters' => array(
        'factories' => array(
            'Organizations/PaginationQuery' => '\Organizations\Repository\Filter\PaginationQueryFactory'
        ),
    ),
    'validators' => array(
        'factories' => array(
        ),
    ),
    'hydrators' => array(
        'factories' => array(
            'Hydrator\Organization' => 'Organizations\Entity\Hydrator\OrganizationHydratorFactory',
        ),
    ),
    'acl' => array(
        'rules' => array(
            // guests are not allowed to see a list of companies
            'guest' => array(
                'deny' => array(
                    'route/lang/organizations',
                ),
            ),
            // recruiters are allowed to view their companies
            'recruiter' => array(
                'allow' => array(
                    'route/lang/organizations',
                ),
            ),

        )
    ),

    'navigation' => array(
        'default' => array(
            'organizations' => array(
                'label' => 'Organizations',
                'route' => 'lang/organizations',
                'order' => 65,                             // allows to order the menu items
                'resource' => 'route/lang/organizations',  // if a resource is defined, the acl will be applied.

                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/organizations',
                    ),
                    'edit' => array(
                        'label' => /*@translate*/ 'Insert',
                        'route' => 'lang/organizations/edit',
                    ),
                ),
            ),

        ),
    ),
);