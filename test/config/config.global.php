<?php

return [
    'view_helper_config' => [
        'asset' => [
            'resource_map' => json_decode(file_get_contents(__DIR__.'/../../public/build/manifest.json'), true),
        ]
    ],
    'core_options' => [
        'publicDir' => sys_get_temp_dir().'/yawik/public',
    ],
];
