<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReleaseTools;

use ReleaseTools\Console\ReleaseController;
use ReleaseTools\Console\SubsplitController;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Module
 */
class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface
{
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return ArrayUtils::merge(ReleaseController::getConsoleUsage(), SubsplitController::getConsoleUsage());
    }
}
