<?php

declare(strict_types=1);

use Applications\Service\ApplicationHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Yawik\Migration\Controller\ConsoleController;
use Yawik\Migration\Handler\MigrationHandler;
use Yawik\Migration\Migrator\Version36 as Migrator36;
use Yawik\Migration\Migrator\Version36\UserImageProcessor;

return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Yawik\Migration\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                'paths' => [ __DIR__ . '/../src/Entity'],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            'Migration/Console' => [ConsoleController::class, 'factory']
        ]
    ],
    'service_manager' => [
        'factories' => [
            Migrator36::class => [Migrator36::class, 'factory'],
            MigrationHandler::class => [MigrationHandler::class, 'factory'],
            OutputInterface::class => function(){
                return new ConsoleOutput();
            }
        ]
    ]
];