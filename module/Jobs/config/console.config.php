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
                        'route'    => 'jobs expire [--days=] [--limit=] [--info]',
                        'defaults' => [
                            'controller' => 'Jobs/Console',
                            'action'     => 'expirejobs',
                            'days'       => 30,
                            'limit'      => '10,0',
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
                'jobs-push-find-external-images-job' => [
                    'options' => [
                        'route' => 'jobs push-find-external-images-job',
                        'defaults' => [
                            'controller' => 'Jobs/Console',
                            'action' => 'pushFetchExternalImagesJob'
                        ],
                    ]
                ]
            ]
        ]
    ]
];
