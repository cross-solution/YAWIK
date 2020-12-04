<?php

declare(strict_types=1);

namespace Yawik\Migration;


use Core\ModuleManager\ModuleConfigLoader;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface
{
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'Yawik Migration Tools',
            'migrate' => 'Migrate Yawik',
        ];
    }

    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__.'/../config');
    }
}