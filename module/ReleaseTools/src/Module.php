<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReleaseTools;

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use ReleaseTools\Console\ReleaseController;
use ReleaseTools\Console\SubsplitController;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Laminas\Stdlib\ArrayUtils;

/**
 * Class Module
 */
class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface,
    VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;

    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return ArrayUtils::merge(ReleaseController::getConsoleUsage(), SubsplitController::getConsoleUsage());
    }
}
