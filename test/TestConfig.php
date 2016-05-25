<?php
return array(
    'modules' => array(
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
    ),
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