<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ReleaseTools\Console\SubsplitController;
use ReleaseTools\Console\ReleaseController;

// Temporary directory to tag the subsplit repos.
// Please note: Only the topmost directory will be removed after the release operations.
$tempDir = sys_get_temp_dir().'/yawik/build';

return [
    'release' => [
        'main_remote_name' => 'origin',
        'subsplit_clone_dir' => $tempDir,
    ],
    'controllers' => [
        'factories' => [
            'release.console.subsplit' => [SubsplitController::class,'factory'],
            'release.console.release' => [ReleaseController::class,'factory']
            
        ]
    ],
    'console' => [
        'router' => [
            'routes' => [
                'subsplit' => [
                    'options' => [
                        'route' => 'subsplit [--source=] [--target=] [--ansi] [--heads=] [--tags=] [--skip-update] [--dry-run] [--verbose|-v] [<module>]',
                        'defaults' => [
                            'controller' => 'release.console.subsplit',
                            'action' => 'index'
                        ]
                    ]
                ],
                'release' => [
                    'options' => [
                        'route' => 'release [--dry-run] [--message=] [--branch=] <tag>',
                        'defaults' => [
                            'controller' => 'release.console.release',
                            'action' => 'index'
                        ]
                    ]
                ],
            ],
        ],
    ],
];
