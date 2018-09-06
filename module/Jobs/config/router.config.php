<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** router.config.php */

// Routes
$routes = [
    'child_routes' => [
        'admin' => [
            'child_routes' => [
                'jobs' => [
                    'type' => 'Segment',
                    'options' => [
                        'route' => '/jobs[/:action]',
                        'defaults' => [
                            'controller' => 'Jobs/Admin',
                            'action'     => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                ],
                'jobs-categories' => [
                    'type' => 'Literal',
                    'options' => [
                        'route' => '/jobs/categories',
                        'defaults' => [
                            'controller' => 'Jobs/AdminCategories',
                            'action'     => 'index',
                        ],
                    ],
                ],
            ]
        ],
        'api-jobs' => array(
            'type' => 'Literal',
            'options' => [
                'route'    => '/api/jobs',
                'defaults' => [
                    'controller' => 'Jobs/ApiJobList',
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => array(
                'completion' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/organization/:organizationId',
                        'defaults' => array(
                            'controller' => 'Jobs/ApiJobListByOrganization',
                            'action' => 'index',
                            'organizationId' => 0,
                        ),
                        'constraints' => array(
                            'organizationId' => '[a-f0-9]+',
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
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/view[/:channel]',
                        'defaults' => array(
                            'controller' => 'Jobs/Template',
                            'action' => 'view',
                            'defaults' => [
                                'channel' => 'default'
                            ]
                        ),
                    ),
                    'may_terminate' => true,
                ),
                'history'   => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/history/:id',
                        'defaults' => array(
                            'controller' => 'Jobs/Manage',
                            'action' => 'history',
                            'defaults' => array(
                                'id' => 0
                            ),
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
                            'constraints' => [
                                'state' => '(pending|approved|declined)',
                            ],
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
                'listOpenJobs' => array(
                    'type' => 'Literal',
                    'options' => [
                        'route' => '/list-pending-jobs',
                        'defaults' => [
                            'controller' => 'Jobs/Approval',
                            'action' => 'listOpenJobs',
                        ],
                    ],
                    'may_terminate' => true,
                ),
            ),
        ),
        'save' => [
            'type' => 'Literal',
            'options' => [
                'route' => '/saveJob',
                'defaults' => [
                    'controller' => 'Jobs/Import',
                    'action' => 'save',
                ],
            ],
            'may_terminate' => true,

        ],

        /**
         * route to the public list job job abs
         */
        'jobboard' => [
            'type' => 'Literal',
            'options' => [
                'route'    => '/jobboard',
                'defaults' => [
                    'controller' => 'Jobs/Jobboard',
                    'action'     => 'index',
                ],
            ],

        ],
        /**
         * route to the public list job job abs
         */
        'landingPage' => [
            'type' => 'Regex',
            'options' => [
                'regex' => '/jobs/(?<q>[a-zA-Z0-9_-]+)\.html$',
#                'route'    => '/jobs/test.html',
                'defaults' => [
                    'controller' => 'Jobs/Jobboard',
                    'action'     => 'index',
                ],
                'spec' => '/jobs/%q%.%format%',
            ],
        ],
        /**
         * route to the public list job job abs
         */
        'export' => [
            'type' => 'Segment',
            'options' => [
                'route'    => '/export[/:format][/:channel]',
                'defaults' => [
                    'controller' => 'Jobs/ApiJobListByChannel',
                    'action'     => 'list',
                    'defaults' => [
                        'format' => 'xml',
                        'channel' => 'default',
                    ],
                    'constraints' => [
                        'format' => '(xml)',
                    ],
                ],
            ],
        ],
    ],
];

return [
    'router' => [
        'routes' => [
            'lang' => $routes
        ]
    ]
];
