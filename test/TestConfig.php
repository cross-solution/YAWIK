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
        'Zend\ServiceManager\Di',
        'Zend\Session',
        'Zend\Router',
        'Zend\Navigation',
        'Zend\Mvc\Plugin\Prg',
        'Zend\Mvc\Plugin\Identity',
        'Zend\Mvc\Plugin\FlashMessenger',
        'Zend\Mvc\I18n',
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
        'Organizations',
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