<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
    'solr' => [
        'connection' => [
            'hostname' => 'yawik',
            'port' => 8443,
            'path' => '/solr',
            'username' => 'yawik',
            'password' => '3qaS2uQU86dGbMXjDds2',
        ]
    ],
    'doctrine' => [
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    'Solr/Event/JobEventSubscriber'
                ]
            ]
        ]
    ]
);