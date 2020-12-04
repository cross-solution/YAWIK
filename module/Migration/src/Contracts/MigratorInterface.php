<?php

declare(strict_types=1);

namespace Yawik\Migration\Contracts;


interface MigratorInterface
{
    /**
     * @return bool True if success
     */
    public function migrate(): bool;

    /**
     * @return string Returns max Yawik version to do migration
     */
    public function version(): string;

    public function getDescription(): string;
}