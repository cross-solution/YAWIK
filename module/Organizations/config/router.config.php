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
                            'profileDetail' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/profile[/:id]',
                                    'constraints' => [
                                        'id' => '\w+',
                                    ],
                                    'defaults' => [
                                        'action' => 'detail',
                                        'controller' => 'Organizations/Profile'
                                    ],
                                ],
                            ],
                            'profile' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/profile',
                                    'defaults' => [
                                        'action' => 'index',
                                        'controller' => 'Organizations/Profile'
                                    ],
                                ],
                            ],
                            'detail' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/detail/:id',
                                    'constraints' => array(
                                        'id' => '\w*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'detail',
                                        'id' => '0',
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
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit[/:id]',
                                    'constraints' => array(
                                        'id' => '\w+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'invite' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/invite[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Organizations/InviteEmployee',
                                        'action' => 'invite',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'my-organization' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/my/organization',
                            'defaults' => array(
                                'controller' => 'Organizations/Index',
                                'action' => 'edit',
                                'id' => '__my__',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
