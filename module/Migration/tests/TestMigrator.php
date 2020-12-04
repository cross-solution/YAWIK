<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests;

use Yawik\Migration\Contracts\MigratorInterface;

class TestMigrator implements MigratorInterface
{
    public function migrate(): bool
    {
        return true;
    }

    public function version(): string
    {
        return "version";
    }

    public function getDescription(): string
    {
        return "Test Migrator";
    }
}