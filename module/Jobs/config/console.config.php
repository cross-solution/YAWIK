<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return [
    'console' => [
        'router' => [
            'routes' => [
                'jobs-expire'    => [
                    'options' => [
                        'route'    => 'jobs expire [--filter=]',
                        'defaults' => [
                            'controller' => 'Jobs/Console',
                            'action'     => 'expirejobs',
                        ],
                    ],
                ],
                'jobs-setpermissions' => [
                    'options' => [
                        'route'    => 'jobs setpermissions',
                        'defaults' => [
                            'controller' => 'Jobs/Console',
                            'action'     => 'setpermissions',
                        ],
                    ],
                ],
            ]
        ]
    ]
];
