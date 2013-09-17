<?php

return array(
    
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
    )),
    
    
    'repositories' => array(
        'invokables' => array(
            'job' => 'Jobs\Repository\Job'
        ),
    ),
    
    'mappers' => array(
        'factories' => array(
            'job' => 'Jobs\Repository\Mapper\JobMapperFactory',
        ),
    ),
    
    'entity_builders' => array(
        'factories' => array(
            'job' => '\Jobs\Repository\EntityBuilder\JobBuilderFactory',
        ),
    ),
);