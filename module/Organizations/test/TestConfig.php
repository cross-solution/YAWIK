<?php
$modules = array_merge(
    include_once __DIR__.'/../../../config/common.modules.php',
    [
        'Core',
        'Auth',
        'Jobs',
        'Organizations'
    ]
);
return array(
    'modules' => $modules,
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
