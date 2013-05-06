<?php
/**
 * Cross Applicant Management
 * Configuration file of the Applications module
 * 
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(
    
    'service_manager' => array(
        'invokables' => array(
            
        ),
        'factories' => array(
            
        ),
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Applications\Controller\Index' => 'Applications\Controller\IndexController',
            'Applications\Controller\Manage' => 'Applications\Controller\ManageController',
            
        ),
    ),
    
    
    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'apply' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/apply',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Applications\Controller',
                                'controller' => 'index',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => false,
                        'child_routes' => array(
                            'form' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:jobid',
                                ),
                            ),
                            'submit' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/submit',
                                ),
                            ),
                        ),
                    ),
                    'applications' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/applications',
                            'defaults' => array(
                                'controller' => '\Applications\Controller\Manage',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'detail' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/detail/:id',
                                    'constraints' => array(
                                        'id' => '.+'
                                    ),
                                    'defaults' => array(
                                        'action' => 'detail',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    // Navigation
    'navigation' => array(
        'default' => array( 
            'apply' => array(
                'label' => 'Applications',
                'route' => 'lang/applications',
//                 'pages' => array(
//                     'facebook' => array(
//                         'label' => 'Facebook',
//                         'route' => 'auth/auth-providers',
//                         'params' => array(
//                             'provider' => 'facebook'
//                         ),
//                      ),
//                 ),
            ),
        ),
    ),
    
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
    // Configure the view service manager
    'view_manager' => array(
        'template_path_stack' => array(
            'Applications' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/apply' => __DIR__ . '/../view/layout/layout.phtml',
            
            'applications/list-row' => __DIR__ . '/../view/partial/list-row.phtml',
            
            // Form partials
            'form/application' => __DIR__ . '/../view/form/application.phtml',
        )
    ),
    
    'view_helpers' => array(
        
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'ApplicationFieldset' => '\Applications\Form\ApplicationFieldset',
            'EducationFieldset' => '\Applications\Form\EducationFieldset',
            'EmploymentFieldset' => '\Applications\Form\EmploymentFieldset',
            'LanguageSkillFieldset' => '\Applications\Form\LanguageSkillFieldset',
            'NativeLanguageFieldset' => '\Applications\Form\NativeLanguageFieldset',
            'SkillFieldset' => '\Applications\Form\SkillFieldset',
            'Application' => '\Applications\Form\CreateApplication',
        ),
        'factories' => array(
            'Application' => '\Applications\Form\ApplicationFactory'
        ),
    ),
    
    
);
