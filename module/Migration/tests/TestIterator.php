<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests;

use Iterator;

class TestIterator implements Iterator
{
    private int $position = 0;

    private array $data = array(
        "user.images",
    );

    public function __construct(array $data) {
        $this->data = $data;
        $this->position = 0;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->data[$this->position]);
    }
}