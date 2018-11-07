<?php
/**
 * Configuration for the DoctrineMongoODMModule
 *
 * Will be merged in the 'doctrine' Key from module.config.php
 */
 
return array(

        'connection' => array(
            'odm_default' => array(
//                'server'           => 'localhost',
//                'port'             => '27017',
//                'connectionString' => 'mongo2.hq.cross:27017',
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
                  'proxy_dir'          => getcwd().'/var/cache/DoctrineMongoODMModule/Proxy',
//                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
//
//                'generate_hydrators' => true,
                  'hydrator_dir'       => getcwd().'/var/cache/DoctrineMongoODMModule/Hydrator',
//                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
//
//                'default_db'         => '',
//
//                'filters'            => array(),  // array('filterName' => 'BSON\Filter\Class'),
//
//                'logger'             => null // 'DoctrineMongoODMModule\Logging\DebugStack'
            )
        ),

        'driver' => array(
            'annotation' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
            ),
            'odm_default' => array(
                'drivers' => array(
                    'Core\Entity' => 'annotation'
                ),
            ),
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
                'subscribers' => array(
                    'Core/DoctrineMongoODM/RepositoryEvents',
                    '\Core\Repository\DoctrineMongoODM\Event\GenerateSearchKeywordsListener',
                    '\Core\Repository\DoctrineMongoODM\Event\PreUpdateDocumentsSubscriber',
                ),
            ),
        ),
);
