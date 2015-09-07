<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** router.config.php */ 

// Routes
return array('router' => array('routes' => array('lang' => array('child_routes' => array(
    'api-jobs' => array(
        'type' => 'Literal',
        'options' => array(
            'route'    => '/api/jobs',
            'defaults' => array(
                'controller' => 'Jobs/ApiJobList',
                'action'     => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'completion' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/organization/:organizationId',
                    'defaults' => array(
                        'controller' => 'Jobs/ApiJobListByOrganization',
                        'action' => 'index',
                        'defaults' => array(
                            'defaults' => array(
                                'organizationId' => 0
                            ),
                            'constraints' => array(
                                'organizationId' => '[a-f0-9]+',
                            ),
                        ),
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'jobs' => array(
        'type' => 'Literal',
        'options' => array(
            'route'    => '/jobs',
            'defaults' => array(
                'controller' => 'Jobs/Index',
                'action'     => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'completion' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/completion/:id',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action' => 'completion',
                        'defaults' => array(
                            'defaults' => array(
                                'id' => 0
                            ),
                            'constraints' => array(
                                'id' => '[a-f0-9]+',
                            ),
                        ),
                    ),
                ),
                'may_terminate' => true,
            ),
            'manage' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:action',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action' => 'edit'
                    ),
                ),
                'may_terminate' => true,
            ),
            'check_apply_id' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/check-apply-id',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action'     => 'check-apply-id',
                        'forceJson' => true, 
                    ),
                    
                ),
                'may_terminate' => true,
            ),
            'view'   => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/view',
                    'defaults' => array(
                        'controller' => 'Jobs/Template',
                        'action' => 'view'
                    ),
                ),
                'may_terminate' => true,
            ),
            'dashboardjobs'   => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard',
                    'defaults' => array(
                        'controller' => 'Jobs/Index',
                        'action' => 'dashboard'
                    ),
                ),
                'may_terminate' => true,
            ),
            'typeahead' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/typeahead',
                    'defaults' => array(
                        'controller' => 'Jobs/Index',
                        'action' => 'typeahead',
                        'forceJson' => true,
                    ),
                ),
                'may_terminate' => true,
            ),

            'editTemplate' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/editTemplate/:id',
                    'defaults' => array(
                        'controller' => 'Jobs/Template',
                        'action' => 'edittemplate',
                        'defaults' => array(
                            'id' => 0
                        ),
                        'constraints' => array(
                            'id' => '[a-f0-9]+',
                        ),
                    ),
                ),
                'may_terminate' => true,
            ),
            'template' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/template/:id/:template',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action'     => 'template',
                        'forceJson' => true,
                    ),
                ),
                'may_terminate' => true,
            ),
            'approval'   => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/approval[/:state]',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action' => 'approval',
                        'defaults' => array(
                            'state' => 'pending'
                        ),
                        'constraints' => array(
                            'state' => '(pending|approved|declined)',
                        ),
                    ),
                ),
                'may_terminate' => true,
            ),
            'deactivate'   => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/deactivate',
                    'defaults' => array(
                        'controller' => 'Jobs/Manage',
                        'action' => 'deactivate',
                    ),
                ),
                'may_terminate' => true,
            ),
            'assign-user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/assign-user',
                    'defaults' => array(
                        'controller' => 'Jobs/AssignUser',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'save' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/saveJob',
            'defaults' => array(
                'controller' => 'Jobs/Import',
                'action' => 'save',
            ),
        ),
        'may_terminate' => true,

    ),

    /**
     * route to the public list job job abs
     */
    'jobboard' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/jobboard',
                'defaults' => array(
                    'controller' => 'Jobs/Jobboard',
                    'action'     => 'index',
                ),
            ),

        ),
)))));
