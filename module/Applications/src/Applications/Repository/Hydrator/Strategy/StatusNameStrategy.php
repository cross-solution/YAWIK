<?php

namespace Applications\Repository\Hydrator\Strategy;

class StatusNameStrategy extends StatusStrategy
{

    public function extract($value)
    {
        return $value->getName();
    }
}