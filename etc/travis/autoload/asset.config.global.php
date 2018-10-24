<?php

$manifest = __DIR__ . '/../../public/build/manifest.json';
if (!is_file($manifest)) {
    file_put_contents($manifest, '{}', LOCK_EX);
}
return [
    'view_helper_config' => [
        'asset' => [
            'resource_map' => json_decode(file_get_contents($manifest), true),
        ]
    ]
];
