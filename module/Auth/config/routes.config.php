<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c] 2013-2016 Cross Solution (http://cross-solution.de]
 * @author cbleek
 * @license   MIT
 */
return [
    'router' => [
        'routes' => [
            'lang' => [
                'child_routes' => [
                    'auth' => [
                        'type' => 'Zend\Router\Http\Literal',
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => 'Auth\Controller\Index',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'my' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/my/:action',
                            'defaults' => [
                                'controller' => 'Auth\Controller\Manage',
                                'action' => 'profile'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'my-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/my/password',
                            'defaults' => [
                                'controller' => 'Auth\Controller\Password'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'my-groups' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/my/groups[/:action]',
                            'defaults' => [
                                'controller' => 'Auth/ManageGroups',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'forgot-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/auth/forgot-password',
                            'defaults' => [
                                'controller' => 'Auth\Controller\ForgotPassword',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'goto-reset-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/auth/goto-reset-password/:token/:userId',
                            'defaults' => [
                                'controller' => 'Auth\Controller\GotoResetPassword',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'register' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/auth/register[/:role]',
                            'defaults' => [
                                'controller' => 'Auth\Controller\Register',
                                'action' => 'index',
                                'role' => 'recruiter'
                            ],
                            'constraints' => [
                                'role' => '(recruiter|user)'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'register-confirmation' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/auth/register-confirmation/:userId',
                            'defaults' => [
                                'controller' => 'Auth\Controller\RegisterConfirmation',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'user-list' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/user/list',
                            'defaults' => [
                                'controller' => 'Auth/Users',
                                'action' => 'list'
                            ]
                        ]
                    ],
                    'user-edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/user/edit/:id',
                            'defaults' => [
                                'controller' => 'Auth/Users',
                                'action' => 'edit'
                            ],
                            'constraints' => [
                                'id' => '\w+'
                            ]
                        ]
                    ],
                    'user-remove' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/user/remove',
                            'defaults' => [
                                'controller' => 'Auth\Controller\Remove',
                                'action' => 'index'
                            ]
                        ]
                    ],
                    'user-switch' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/user/switch',
                            'defaults' => [
                                'controller' => 'Auth/Users',
                                'action' => 'switch',
                            ],
                        ],
                    ],
                ],
            ],
            'auth-provider' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/login/:provider',
                    'constraints' => [
                    // 'provider' => '.+',
                    ],
                    'defaults' => [
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'login'
                    ]
                ]
            ],
            'auth-hauth' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/login/hauth',
                    'defaults' => [
                        'controller' => 'Auth\Controller\HybridAuth',
                        'action' => 'index'
                    ]
                ]
            ],
            // This route must be after auth-provider for the
            // last in first out order of the route stack!
            // @TODO implement auth-provider and auth-extern as child routes
            // to a new auth-login route.
            'auth-extern' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/login/extern',
                    'defaults' => [
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'login-extern',
                        'forceJson' => true
                    ]
                ],
                'may_terminate' => true
            ],
            'auth-social-profiles' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/auth/social-profiles',
                    'defaults' => [
                        'controller' => 'Auth/SocialProfiles',
                        'action' => 'fetch'
                    ]
                ]
            ],
            
            'auth-group' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/auth/groups',
                    'defaults' => [
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'group',
                        'forceJson' => true
                    ]
                ],
                'may_terminate' => true
            ],
            'auth-logout' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => 'Auth\Controller\Index',
                        'action' => 'logout'
                    ]
                ]
            ],
            'user-image' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/image/:id',
                    'defaults' => [
                        'controller' => 'Auth\Controller\Image',
                        'action' => 'index',
                        'id' => 0
                    ]
                ]
            ],
            'user-search' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/user/search',
                    'defaults' => [
                        'controller' => 'Auth/ManageGroups',
                        'action' => 'search-users'
                    ]
                ]
            ],
            'test-hybrid' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/testhybrid',
                    'defaults' => [
                        'controller' => 'Auth/SocialProfiles',
                        'action' => 'testhybrid'
                    ]
                ]
            ]
        ]
    ]
];
