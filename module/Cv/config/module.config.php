<?php
return array(
    
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Cv\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => array( __DIR__ . '/../src/Cv/Entity'),
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    '\Cv\Repository\Event\InjectContactListener',
                    '\Cv\Repository\Event\DeleteRemovedAttachmentsSubscriber',
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
    
    // Routes
    /* DISABLED until module is fixed */
    // TODO: Remove comments when module is fixed.
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'cvs' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/cvs',
                            'defaults' => array(
                                'controller' => 'Cv/Index',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'create' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'Cv\Controller\Manage',
                                        'action' => 'form'
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    'acl' => array(
        'rules' => array(
            'user' => array(
                'allow' => array(
                    'route/lang/cvs',
                    'Cv\Controller\Manage',
                ),
            ),
            'admin' => [
                'deny' => [
                    'route/lang/cvs',
                    'Cv\Controller\Manage',
                ]
            ]
        ),
    ),
    
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            //'Cv\Controller\Index' => 'Cv\Controller\IndexController',
            'Cv\Controller\Manage' => 'Cv\Controller\ManageController',
        ),
        'factories' => array(
            'Cv/Index' => 'Cv\Factory\Controller\IndexControllerFactory',
        ),
    ),
    
    // Navigation
    // Disabled until module is fixed
    // TODO: Remove comments when module is fixed
    'navigation' => array(
        'default' => array(
            'resume' => array(
                'label' =>  /*@translate*/ 'Talent-Pool',
                'route' => 'lang/cvs',
                'resource' => 'route/lang/cvs',
                'order' => 10,
                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/cvs',
                    ),
                    'create' => array(
                        'label' => /*@translate*/ 'Create resume',
                        'route' => 'lang/cvs/create',
                    ),
                ),
            ),
        ),
    ),
    
    'view_manager' => array(
        
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            //'form/div-wrapper-fieldset' => __DIR__ . '/../view/form/div-wrapper-fieldset.phtml',
        ),
    
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'filters' => array(
        'invokables' => [
            //'Cv/PaginationQuery' => 'Cv\Repository\Filter\PaginationQuery',
        ],
        'factories' => array(
            'Cv/PaginationQuery' => 'Cv\Repository\Filter\PaginationQueryFactory',
        ),
    ),

    'paginator_manager' => array(
        'factories' => array(
            'Cv/Paginator' => 'Cv\Paginator\PaginatorFactory',
        ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'CvContainer'       => '\Cv\Form\CvContainer',
            'EducationFieldset' => '\Cv\Form\EducationFieldset',
            'EmploymentFieldset' => '\Cv\Form\EmploymentFieldset',
            'SkillFieldset' => '\Cv\Form\SkillFieldset',
            'LanguageSkillFieldset' => '\Cv\Form\LanguageFieldset',
            'CvEmploymentForm' => '\Cv\Form\EmploymentForm',
            'CvEducationForm' => '\Cv\Form\EducationForm',
            'CvSkillForm' => '\Cv\Form\SkillForm',
            'Cv/PreferredJobForm' => 'Cv\Form\PreferredJobForm',
            'Cv/LanguageSkillForm' => '\Cv\Form\LanguageSkillForm',
            'Cv/LanguageSkillFieldset' => '\Cv\Form\LanguageSkillFieldset',
            'Cv/NativeLanguageForm' => '\Cv\Form\NativeLanguageForm',
            'Cv/NativeLanguageFieldset' => '\Cv\Form\NativeLanguageFieldset',
            'Cv/SearchForm' => '\Cv\Form\SearchForm',
        ),
        'factories' => array(
            'CvEmploymentCollection' => '\Cv\Form\EmploymentCollectionFactory',
            'CvEducationCollection' => '\Cv\Form\EducationCollectionFactory',
            'CvSkillCollection' => '\Cv\Form\SkillCollectionFactory',
            'Cv/LanguageSkillCollection' => '\Cv\Form\LanguageSkillCollectionFactory',
            'CvContactImage' => '\Cv\Form\CvContactImageFactory',
            'Cv/PreferredJobFieldset' => '\Cv\Form\PreferredJobFieldsetFactory',
            'Cv/SearchFormFieldset' => '\Cv\Form\SearchFormFieldsetFactory',
            'Cv/Attachments' => '\Cv\Form\AttachmentsFormFactory',
        ),
    ),
    
    'options' => [
        'Cv/Options' => [
            'class' => '\Cv\Options\ModuleOptions'
        ]
    ]
);
