<?php

declare(strict_types=1);

return [
    'console' => [
        'router' => [
            'routes' => [
                'migrate36' => [
                    'options' => [
                        'route' => 'migrate',
                        'defaults' => [
                            'controller' => 'Migration/Console',
                            'action' => 'migrate'
                        ]
                    ]
                ]
            ]
        ]
    ],
];