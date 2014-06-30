<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

return array('router' => array('routes' => array('lang' => array('child_routes' => array(
    'apply' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/apply/:applyId',
            'defaults' => array(
                'controller' => 'Applications\Controller\Apply',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        /*'child_routes' => array(
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
        ),*/
    ),
    'applications-dashboard' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/applications-dashboard',
            'defaults' => array(
                'controller' => '\Applications\Controller\Index',
                'action'     => 'dashboard'
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
                            'applications-list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/multi/:action',
                                    'defaults' => array(
                                        'controller' => 'Applications\Controller\MultiManage',
                                        'action' => 'multimodal'
                                    ),
                                ),
                            ),
                        ),
                    ),
)))));
