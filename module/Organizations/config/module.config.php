<?php
/**
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */

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
                    '\Organizations\Repository\Event\InjectOrganizationReferenceListener',
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


    'controllers' => array(
        'factories' => array(
            'Organizations/TypeAHead' => 'Organizations\Controller\SLFactory\TypeAHeadControllerSLFactory',
            'Organizations/Index' => 'Organizations\Controller\SLFactory\IndexControllerSLFactory',
        )
    ),
    'view_manager' => array(
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
             'organizations/index/edit' => __DIR__ . '/../view/organizations/index/form.phtml',
             'organizations/form/employees-fieldset' => __DIR__ . '/../view/form/employees-fieldset.phtml',
             'organizations/form/employee-fieldset' => __DIR__ .'/../view/form/employee-fieldset.phtml',
        ),
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
             'Organizations/form' => 'Organizations\Form\Organizations',
             'Organizations/OrganizationsContactForm'     => 'Organizations\Form\OrganizationsContactForm',
             'Organizations/OrganizationsNameForm'        => 'Organizations\Form\OrganizationsNameForm',
            'Organizations/OrganizationsDescriptionForm' => 'Organizations\Form\OrganizationsDescriptionForm',
            'Organizations/OrganizationsContactFieldset' => 'Organizations\Form\OrganizationsContactFieldset',
            'Organizations/OrganizationsNameFieldset'    => 'Organizations\Form\OrganizationsNameFieldset',
            'Organizations/OrganizationsDescriptionFieldset' => 'Organizations\Form\OrganizationsDescriptionFieldset',
            //'Organizations/OrganizationFieldset'       => 'Organizations\Form\OrganizationFieldset',
            'Organizations/EmployeesContainer'           => 'Organizations\Form\EmployeesContainer',
            'Organizations/Employees'                    => 'Organizations\Form\Employees',


            'Organizations/Employee'                     => 'Organizations\Form\Element\Employee',
        ),
        'factories' => array(
            'Organizations/Image' => 'Organizations\Form\LogoImageFactory',
            'Organizations/EmployeesFieldset'            => 'Organizations\Factory\Form\EmployeesFieldsetFactory',
            'Organizations/EmployeeFieldset'             => 'Organizations\Factory\Form\EmployeeFieldsetFactory',
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
                'allow' => array(
                    'Entity/OrganizationImage'
                ),
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
        ),
        'assertions' => array(
            'invokables' => array(
            ),
        ),
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