<?php
namespace Cv;

use Cv\Controller\ManageController;
use Cv\Form\InputFilter\Education;
use Cv\Form\InputFilter\Employment;
use Cv\Form\PreferredJobFieldset;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Cv\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => [ __DIR__ . '/../src/Entity'],
            ],
        ],
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    '\Cv\Repository\Event\InjectContactListener',
                    '\Cv\Repository\Event\DeleteRemovedAttachmentsSubscriber',
                    '\Cv\Repository\Event\UpdateFilesPermissionsSubscriber',
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
    
    // Routes
    /* DISABLED until module is fixed */
    // TODO: Remove comments when module is fixed.
    'router' => [
        'routes' => [
            'lang' => [
                'child_routes' => [
                    'cvs' => [
                        'type' => 'Literal',
                        'options' => [
                            'route'    => '/cvs',
                            'defaults' => [
                                'controller' => 'Cv/Index',
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'create' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/create',
                                    'defaults' => [
                                        'controller' => 'Cv\Controller\Manage',
                                        'action' => 'form'
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/edit/:id',
                                    'defaults' => [
                                        'controller' => 'Cv\Controller\Manage',
                                        'action' => 'form'
                                    ],
                                ],
                            ],
                            'view' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/view/:id',
                                    'defaults' => [
                                        'controller' => 'Cv/View',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'my-cv' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/my/cv',
                            'defaults' => [
                                'controller' => 'Cv\Controller\Manage',
                                'action' => 'form',
                                'id' => '__my__'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    
    'acl' => [
        'rules' => [
            'user' => [
                'allow' => [
                    'route/lang/my-cv',
                    'Cv\Controller\Manage',
                    'navigation/resume-user',
                    'Cv/Status' => ['change'],
                ],
            ],
            'recruiter' => [
                'deny' => [
                    'route/lang/my-cv',
                    'navigation/resume-user',
                    'Cv/Status',
                ],
                'allow' => [
                    'route/lang/cvs',
                    'navigation/resume-recruiter',
                    'Entity/Cv' => [
                        'view' => 'Cv/MayView',
                        'edit' => 'Cv/MayChange',
                    ],
                ],
            ],
        ],
        'assertions' => [
            'invokables' => [
                'Cv/MayView'   => 'Cv\Acl\Assertion\MayViewCv',
                'Cv/MayChange' => 'Cv\Acl\Assertion\MayChangeCv',
            ],
        ],
    ],
    
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => [
        'factories' => [
            \Cv\Controller\IndexController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            'Cv/View'  => 'Cv\Factory\Controller\ViewControllerFactory',
            'Cv\Controller\Manage' => [ManageController::class,'factory'],
        ],
        'aliases' => [
            'Cv/Index' => \Cv\Controller\IndexController::class,
        ]
    ],
    
    // Navigation
    // Disabled until module is fixed
    // TODO: Remove comments when module is fixed
    'navigation' => [
        'default' => [
            'resume-recruiter' => [
                'label' =>  /*@translate*/ 'Talent-Pool',
                'route' => 'lang/cvs',
                'active_on' => [ 'lang/cvs/edit', 'lang/cvs/view' ],
                'resource' => 'navigation/resume-recruiter',
                'order' => 10,
                'query' => [
                    'clear' => '1'
                ],
                'pages' => [
                    'list' => [
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/cvs',
                    ],
                    'create' => [
                        'label' => /*@translate*/ 'Create resume',
                        'route' => 'lang/cvs/create',
                    ],
                ],
            ],
            'resume-user' => [
                'label' => /*@translate*/ 'Resume',
                'route' => 'lang/my-cv',
                'resource' => 'navigation/resume-user',
                'order' => 10
            ],
        ],
    ],
    
    'view_manager' => [
        
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => [
            'cv/form/employment.view' => __DIR__ . '/../view/cv/form/employment.view.phtml',
            'cv/form/employment.form' => __DIR__ . '/../view/cv/form/employment.form.phtml',
            'cv/form/education.view' => __DIR__ . '/../view/cv/form/education.view.phtml',
            'cv/form/education.form' => __DIR__ . '/../view/cv/form/education.form.phtml'
        ],
    
        // Where to look for view templates not mapped above
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'filters' => [
        'factories' => [
            'Cv/PaginationQuery' => 'Cv\Repository\Filter\PaginationQueryFactory',
        ],
    ],
    
    'input_filters' => [
        'aliases' => [
            'Cv/Employment' => Employment::class,
            'Cv/Education' => Education::class
        ],
        'invokables' => [
            'Cv/Employment' => Employment::class,
            'Cv/Education' => Education::class
        ],
    ],

    'paginator_manager' => [
        'factories' => [
            'Cv/Paginator' => 'Cv\Paginator\PaginatorFactory',
        ],
    ],
    
    'form_elements' => [
        'invokables' => [
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
        ],
        'factories' => [
            'CvEmploymentCollection' => '\Cv\Factory\Form\EmploymentCollectionFactory',
            'CvEducationCollection' => '\Cv\Factory\Form\EducationCollectionFactory',
            'CvSkillCollection' => '\Cv\Factory\Form\SkillCollectionFactory',
            'Cv/LanguageSkillCollection' => '\Cv\Factory\Form\LanguageSkillCollectionFactory',
            'CvContactImage' => '\Cv\Factory\Form\CvContactImageFactory',
            PreferredJobFieldset::class => InvokableFactory::class,
            'Cv/SearchFormFieldset' => '\Cv\Factory\Form\SearchFormFieldsetFactory',
            'Cv/Attachments' => '\Cv\Factory\Form\AttachmentsFormFactory',
        ],
    ],
    
    'options' => [
        'Cv/Options' => [
            'class' => '\Cv\Options\ModuleOptions'
        ]
    ]
];
