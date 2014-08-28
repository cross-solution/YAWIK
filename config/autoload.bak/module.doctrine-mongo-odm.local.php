<?php
return array(
    'doctrine' => array(

        'connection' => array(
            'odm_default' => array(
//                'server'           => 'localhost',
//                'port'             => '27017',
//                'connectionString' => null,
//                'user'             => null,
//                'password'         => null,
//                'dbname'           => null,
//                'options'          => array()
            ),
        ),

        'configuration' => array(
            'odm_default' => array(
//                'metadata_cache'     => 'array',
//
//                'driver'             => 'odm_default',
//
//                'generate_proxies'   => true,
//                'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
//                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
//
//                'generate_hydrators' => true,
//                'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
//                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
//
//                'default_db'         => null,
//
//                'filters'            => array(),  // array('filterName' => 'BSON\Filter\Class'),
//
//                'logger'             => null // 'DoctrineMongoODMModule\Logging\DebugStack'
            )
        ),

        'driver' => array(
            'odm_default' => array(
//                'drivers' => array()
            )
        ),

        'documentmanager' => array(
            'odm_default' => array(
//                'connection'    => 'odm_default',
//                'configuration' => 'odm_default',
//                'eventmanager' => 'odm_default'
            )
        ),

        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array()
            )
        ),
    ),
);