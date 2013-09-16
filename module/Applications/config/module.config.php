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
                                'controller' => 'Applications\Controller\Index',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => false,
                        'child_routes' => array(
                            'form' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:jobId',
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
                                        'id' => '[a-z0-9]+',
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
                'order' => 20,
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
            
            'pagination-control' => __DIR__ . '/../view/partial/pagination-control.phtml',
            
        )
    ),
    
    'view_helpers' => array(
        
    ),
    
    'form_elements' => array(
         'invokables' => array(
//             'ApplicationFieldset' => '\Applications\Form\ApplicationFieldset',
//             'EducationFieldset' => '\Applications\Form\EducationFieldset',
//             'EmploymentFieldset' => '\Applications\Form\EmploymentFieldset',
//             'LanguageFieldset' => '\Applications\Form\LanguageFieldset',
             'Application' => '\Applications\Form\CreateApplication',
         ),
//         'factories' => array(
//             'Application' => '\Applications\Form\ApplicationFactory'
//         ),
     ),
     
     'filters' => array(
         'factories'=> array(
             'applications-params-to-properties' => '\Applications\Filter\ParamsToPropertiesFactory'
         ),
     ),
     
     'repositories' => array(
         'invokables' => array(
             'Application' => 'Applications\Repository\Application',
         ),
     ),
     
     'mappers' => array(
         'factories' => array(
             //'Application' => 'Applications\Repository\Mapper\ApplicationMapperFactory'
         ),
         'abstract_factories' => array(
             'Applications\Repository\Mapper\AbstractMapperFactory'
         ),
     ),
    
     'entity_builders' => array(
         'factories' => array(
             'Application' => '\Applications\Repository\EntityBuilder\ApplicationBuilderFactory',
             'JsonApplication' => '\Applications\Repository\EntityBuilder\JsonApplicationBuilderFactory',
             'Application-Cv' => '\Applications\Repository\EntityBuilder\CvBuilderFactory',
             'JsonApplicationCv' => '\Applications\Repository\EntityBuilder\JsonCvBuilderFactory',
             'application-contact' => '\Applications\Repository\EntityBuilder\ContactBuilderFactory',
             'application-cv-skill' => '\Cv\Repository\EntityBuilder\SkillBuilderFactory',
             'json-application-contact' => '\Applications\Repository\EntityBuilder\JsonContactBuilderFactory',
             'Application-Cv-Education' => '\Applications\Repository\EntityBuilder\EducationBuilderFactory',
             'Application-Cv-Employment' => '\Applications\Repository\EntityBuilder\EmploymentBuilderFactory',
         ),
     ),
    
);
