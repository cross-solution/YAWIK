<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'odm_default' => array(
                  'server'           => 'mongo2.hq.cross',
                  'port'             => '27017',
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
//                'proxy_dir'          => 'cache/DoctrineMongoODMModule/Proxy',
//                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
//
//                'generate_hydrators' => true,
//                'hydrator_dir'       => 'cache/DoctrineMongoODMModule/Hydrator',
//                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
//
                  'default_db'         => 'CrossApplicantManagement2',
                  //'default_db'         => 'YawikDemo',
//
//                'filters'            => array(),  // array('filterName' => 'BSON\Filter\Class'),
//
//                'logger'             => null // 'DoctrineMongoODMModule\Logging\DebugStack'
            )
        ),   
    ),
);