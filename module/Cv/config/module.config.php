<?php
return array(
    
    // Service Manager
    'service_manager' => array(
        'factories' => array(
            'CvRepository' => 'Cv\Repository\Service\CvRepositoryFactory',
            'CvMapper'     => 'Cv\Repository\Service\CvMapperFactory',
            'CvBuilder'    => 'Cv\Repository\Service\CvBuilderFactory',
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
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'cvs' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/cvs',
                            'defaults' => array(
                                'controller' => 'Cv\Controller\Index',
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
                                        'action' => 'form',
                                        'id' => false,
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                            'save' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/save',
                                    'defaults' => array(
                                        'controller' => 'Cv\Controller\Manage',
                                        'action' => 'save',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            'Cv\Controller\Index' => 'Cv\Controller\IndexController',
            'Cv\Controller\Manage' => 'Cv\Controller\ManageController',
        ),
    ),
    
    // Navigation
    'navigation' => array(
        'default' => array(
            'resume' => array(
                'label' =>  /*@translate*/ 'Resumes',
                'route' => 'lang/cvs',
                'order' => 10,
                //'route' => 'lang/cvs',
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
    
    'form_elements' => array(
        'invokables' => array(
            'CvForm'            => '\Cv\Form\Cv',
            'CvFieldset'        => '\Cv\Form\CvFieldset',
            'EducationFieldset' => '\Cv\Form\EducationFieldset',
            'EmploymentFieldset' => '\Cv\Form\EmploymentFieldset',
        	'SkillFieldset' => '\Cv\Form\SkillFieldset',
        	'NativeLanguageFieldset' => '\Cv\Form\NativeLanguageFieldset',
        	'LanguageSkillFieldset' => '\Cv\Form\LanguageFieldset',
        		
            
        ),
        'factories' => array(
            'Cv' => '\Cv\Form\CvFactory',
            'EducationCollection' => '\Cv\Form\EducationCollectionFactory',
        ),
    ),
    
    'repositories' => array(
        'invokables' => array(
            'cv' => '\Cv\Repository\Cv',
        ),
    ),
    
    'mappers' => array(
        'factories' => array(
            'cv' => '\Cv\Repository\Mapper\CvMapperFactory'
        ),
    ),
    
    'entity_builders' => array(
        'factories' => array(
            'cv' => '\Cv\Repository\EntityBuilder\CvBuilderFactory',
            'education' => '\Cv\Repository\EntityBuilder\EducationBuilderFactory',
            'employment' => '\Cv\Repository\EntityBuilder\EmploymentBuilderFactory',
        ),
    ),
    
);