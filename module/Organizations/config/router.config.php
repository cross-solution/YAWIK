<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */
return array(
    'router' => array(
        'routes' => array(
            'lang' => array(
                'child_routes' => array(
                    'organizations' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/organizations',
                            'defaults' => array(
                                'controller' => 'Organizations/Index',
                                'action' => 'index',
                                'module' => 'Organizations',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'detail' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:id',
                                    'constraints' => array(
                                        'id' => '\w+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'detail',
                                    ),
                                ),
                            ),
                            'logo' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/logo/:id',
                                    'constraints' => array(
                                        'id' => '\w+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'logo',
                                    ),
                                ),
                            ),
                            'form' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/form',
                                    'defaults' => array(
                                        'action' => 'form',
                                    ),
                                ),
                            ),
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list',
                                    'defaults' => array(
                                        'action' => 'list',
                                    ),
                                ),
                            ),
                            'update' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/change/:id',
                                    'constraints' => array(
                                        'id' => '\w+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'change',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'organizationsTestfill' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/organizationstest',
                            'defaults' => array(
                                'controller' => 'Organizations/Index',
                                'action' => 'testfill',
                                'module' => 'Organizations',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                )
            )
        )
    )
);
