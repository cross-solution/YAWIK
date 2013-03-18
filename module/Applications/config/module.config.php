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
            
        ),
    ),
    
    
    // Routes
    'router' => array(
        'routes' => array(
            'main' => array(
                'child_routes' => array(
                    'apply' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
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
                                    'defaults' => array(
                                        //'__NAMESPACE__' => 'Applications\Controller',
                                        //'controller' => 'index',
                                        //'action' => 'apply',
                                     ),
                                ),
                            ),
                            'submit' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/submit',
                                    'defaults' => array(
                                        //'__NAMESPACE__' => 'Applications\Controller',
                                        //'controller' => 'index',
                                        'action' => 'submit',
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
//     'navigation' => array(
//         'default' => array( 
//             'login' => array(
//                 'label' => 'Login',
//                 'route' => 'auth',
//                 'pages' => array(
//                     'facebook' => array(
//                         'label' => 'Facebook',
//                         'route' => 'auth/auth-providers',
//                         'params' => array(
//                             'provider' => 'facebook'
//                         ),
//                      ),
//                 ),
//             ),
//         ),
//     ),
    
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
            
            // Form partials
            'form/application' => __DIR__ . '/../view/form/application.phtml',
        )
    ),
    
    'view_helpers' => array(
        
    ),
    
    
);
