<?php

namespace Applications\Repository\EntityBuilder;

abstract class AbstractEntityBuilder extends \Core\Repository\EntityBuilder
{
    public function __construct()
    {
        parent::__construct(
            $this->constructHydrator(),
            $this->constructEntityPrototype(),
            $this->constructCollectionPrototype()    
        );
    }
    
    protected function constructHydrator()
    {
        return new \Core\Repository\Hydrator\EntityHydrator();
    }
    
    abstract protected function constructEntityPrototype();
    
    protected function constructCollectionPrototype()
    {
        return new \Core\Entity\Collection();
    }
}