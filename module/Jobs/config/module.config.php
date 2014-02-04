<?php

return array(
        'doctrine' => array(
                'driver' => array(
                        'odm_default' => array(
                                'drivers' => array(
                                        'Jobs\Entity' => 'annotation',
                                ),
                        ),
                ),
        ),
        

    'Jobs' => array(
        'dashboard' => array(
            'enabled' => true,
            'widgets' => array(
                'recentJobs' => array(
                    'controller' => 'Jobs\Controller\Index',
                    'params' => array(
                        'type' => 'recent'
                    ),
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
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'jobs' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/jobs',
                            'defaults' => array(
                                'controller' => 'Jobs\Controller\Index',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'save' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/saveJob',
                                    'defaults' => array(
                                        'controller' => 'Jobs\Controller\Manage',
                                        'action' => 'save',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                ),
            ),
        ),
    ),
    
    'acl' => array(
        'rules' => array(
            'user' => array(
                'allow' => array(
                    'Jobs\Controller\Manage'
                ),
            ),                
        ),
    ),
    
    'navigation' => array(
        'default' => array(
            'jobs' => array(
                'label' =>  /*@translate*/ 'Jobs',
                'route' => 'lang/jobs',
                'order' => '30',
            ),
        ),
    ),
    
    
    'controllers' => array(
        'invokables' => array(
            'Jobs\Controller\Index' => 'Jobs\Controller\IndexController',
            'Jobs\Controller\Manage' => 'Jobs\Controller\ManageController',
        ),
    ),
    
    'view_manager' => array(
    
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'jobs/sidebar/index' => __DIR__ . '/../view/sidebar/index.phtml',
            'jobs/form/list-filter' => __DIR__ . '/../view/form/list-filter.phtml',
            //'form/div-wrapper-fieldset' => __DIR__ . '/../view/form/div-wrapper-fieldset.phtml',
        ),
    
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
       
    'form_elements' => array(
        'invokables' => array(
            'JobForm'            => '\Jobs\Form\Job',
            'JobFieldset'        => '\Jobs\Form\JobFieldset',
            'Jobs/ListFilter'    => '\Jobs\Form\ListFilter',
            'Jobs/ListFilterFieldset' => 'Jobs\Form\ListFilterFieldset',
        ),
        'factories' => array(
            'jobs/ListFilterFieldsetExtended' => 'Jobs\Form\ListFilterFieldsetExtendedFactory',
        )
    ),
    
    'filters' => array(
        'factories'=> array(
            'Jobs/PaginationQuery' => '\Jobs\Repository\Filter\PaginationQueryFactory'
        ),
    ),
    
);