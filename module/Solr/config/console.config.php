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
                'solr-index-job'    => [
                    'options' => [
                        'route'    => 'solr index job',
                        'defaults' => [
                            'controller' => 'Solr/Console',
                            'action'     => 'activeJobIndex',
                        ],
                    ],
                ],
            ]
        ]
    ]
];