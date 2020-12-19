<?php

declare(strict_types=1);

namespace Yawik\Migration\Contracts;


interface ProcessorInterface
{
    public function process(): bool;
}