<?php
return array(
    'doctrine' => array(
        'configuration' => array(
            'odm_default' => array(
                'default_db' => 'YAWIK_TEST',
            ),
        ),
    ),
    'modules' => array(
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'Install',
        'Core',
        'Auth',
        'Jobs',
        'Geo',
        'Cv',
        'Settings',
        'Applications',
        'Orders',
        'Organizations',
        'Solr'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),

        'config_glob_paths' => array(
            __DIR__  .  '/../test/config/{,*.}{global,local}.php',
        ),
    )
);