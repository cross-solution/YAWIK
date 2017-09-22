<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'apply' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/apply[/:channel]/:applyId',
                            'defaults' => [
                                'controller' => 'Applications\Controller\Apply',
                                'action' => 'index',
                                'defaults' => [
                                    'channel' => 'default'
                                ]

                            ]
                        ],
                        'may_terminate' => true
                    ],
                    'apply-one-click' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/apply-one-click/:applyId/:network[/:immediately]',
                            'defaults' => array(
                                'controller' => 'Applications\Controller\Apply',
                                'action' => 'oneClickApply'
                            ),
                            'constraints' => array(
                                'network' => 'facebook|xing|linkedin',
                                'immediately' => '0|1'
                            )
                        )
                    ),
                    'applications-dashboard' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/applications-dashboard',
                            'defaults' => array(
                                'controller' => 'Applications\Controller\Index',
                                'action' => 'dashboard'
                            )
                        )
                    ),
                    'applications' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/applications',
                            'defaults' => array(
                                'controller' => 'Applications/Controller/Manage',
                                'action' => 'index'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'detail' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id',
                                    'constraints' => array(
                                        'id' => '[a-z0-9]+'
                                    ),
                                    'defaults' => array(
                                        'action' => 'detail'
                                    )
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'status' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:status',
                                            'defaults' => array(
                                                'action' => 'status',
                                                'status' => 'bad'
                                            ),
                                            'constraints' => array(
                                                'status' => '[a-z]+'
                                            )
                                        )
                                    )
                                )
                            ),
                            'disclaimer' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/disclaimer',
                                    'defaults' => array(
                                        'controller' => 'Applications\Controller\Index',
                                        'action' => 'disclaimer'
                                    )
                                ),
                                'may_terminate' => true
                            ),
                            'comments' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/comments/:action',
                                    'defaults' => array(
                                        'controller' => 'Applications/CommentController'
                                    )
                                )
                            ),
                            'applications-list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/multi/:action',
                                    'defaults' => array(
                                        'controller' => 'Applications\Controller\MultiManage',
                                        'action' => 'multimodal'
                                    )
                                )
                            ),
                        )
                    )
                )
            )
        )
    )
);
