<?php
$commonModules = include_once __DIR__.'/../../../config/common.modules.php';

return array(
    'modules' => array_merge($commonModules,array(
        'Core',
        'Auth',
        'Jobs',
        'Applications',
        'Geo',
        'Organizations'
    )),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),

        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    )
);
