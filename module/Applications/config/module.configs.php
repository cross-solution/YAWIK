<?php

/**
 * Cross Applicant Management
 * Configuration file of the Applications module
 * 
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(

    'doctrine' => array(
       'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Applications\Entity' => 'annotation',
                ),
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    '\Applications\Repository\Event\JobReferencesUpdateListener',
                ),
            ),
        ),
    ),
    
    'Applications' => array(
        'dashboard' => array(
            'enabled' => true,
            'widgets' => array(
                'recentApplications' => array(
                    'script' => 'applications/dashboard/recent',
                ),
            ),
        ),
    
        'allowedMimeTypes' => array('image', 'applications/pdf'),
        'settings' => array(
            'entity' => '\Applications\Entity\Settings',
            'navigation_order' => 30,
        ),
    ),
    
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
            'Applications/CommentController' => 'Applications\Controller\CommentController',
            'Applications/Console' => 'Applications\Controller\ConsoleController'
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
                            'route' => '/apply',
                            'defaults' => array(
                                'controller' => 'Applications\Controller\Index',
                                'action' => 'index',
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
                    'disclaimer' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/disclaimer',
                            'defaults' => array(
                                'controller' => '\Applications\Controller\Index',
                                'action' => 'disclaimer',
                            ),
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
                                    'route' => '/:id',
                                    'constraints' => array(
                                        'id' => '[a-z0-9]+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'detail',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    
                                    'status' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:status',
                                            'defaults' => array(
                                                'action' => 'status',
                                                'status' => 'bad',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'comments' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/comments/:action',
                                    'defaults' => array(
                                        'controller' => 'Applications/CommentController',
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
                    'route/lang/applications',
                    'Applications\Controller\Manage',
                    'Entity/Application' => array(
                        '__ALL__' => 'Applications/Access',
                        
                    ),
                ),
            ),
        ),
        'assertions' => array(
            'invokables' => array(
                'Applications/Access'      => 'Applications\Acl\ApplicationAccessAssertion',
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
                'resource' => 'route/lang/applications',
                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Overview',
                        'route' => 'lang/applications',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
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
            'applications/sidebar/manage' => __DIR__ . '/../view/sidebar/manage.phtml',
            'applications/dashboard/recent' => __DIR__ . '/../view/applications/index/dashboard.phtml',
            'applications/index/disclaimer' => __DIR__ . '/../view/applications/index/disclaimer.phtml',
   //         'pagination-control' => __DIR__ . '/../../Core/view/partial/pagination-control.phtml',
        )
    ),
    'view_helpers' => array(
        
    ),
    
    
    'view_inject_headscript' => array(
        'lang/applications' => 'Core/js/jquery.barrating.min.js',
    ),
    'form_elements' => array(
        'invokables' => array(
//             'ApplicationFieldset' => '\Applications\Form\ApplicationFieldset',
//             'EducationFieldset' => '\Applications\Form\EducationFieldset',
//             'EmploymentFieldset' => '\Applications\Form\EmploymentFieldset',
//             'LanguageFieldset' => '\Applications\Form\LanguageFieldset',
             'Application' => '\Applications\Form\CreateApplication',
             'Applications/Mail' => 'Applications\Form\Mail',
             'Applications/BaseFieldset' => 'Applications\Form\BaseFieldset', 
             'Applications/Privacy' => 'Applications\Form\PrivacyFieldset', 
             'Applications/SettingsFieldset' => 'Applications\Form\SettingsFieldset',
             'Applications/CommentForm' => 'Applications\Form\CommentForm',
             'Applications/CommentFieldset' => 'Applications\Form\CommentFieldset',
         ),
        'factories' => array(
            'Applications/ContactFieldset' => 'Applications\Form\ContactFieldsetFactory',
            'Applications/AttachmentsCollection' => '\Applications\Form\AttachmentsCollectionFactory',
            'Applications/AttachmentsFieldset' => '\Applications\Form\AttachmentsFieldsetFactory',
        ),
     ),
     
    'filters' => array(
        'invokables' => array(
            'Applications/ActionToStatus' => 'Applications\Filter\ActionToStatus',
        ),
        'factories'=> array(
            'Applications/PaginationQuery' => '\Applications\Repository\Filter\PaginationQueryFactory'
        ),
    ),
     
    'mails' => array(
        'invokables' => array(
            'Applications/NewApplication' => 'Applications\Mail\NewApplication',
            'Applications/StatusChange'   => 'Applications\Mail\StatusChange',
            'Applications/Forward'        => 'Applications\Mail\Forward',
        ),
    ),
    
);
