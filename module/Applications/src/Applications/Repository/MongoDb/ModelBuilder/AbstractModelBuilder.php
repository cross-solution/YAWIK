<?php

namespace Applications\Repository\MongoDb\ModelBuilder;

abstract class AbstractModelBuilder extends \Core\Repository\ModelBuilder
{
    public function __construct()
    {
        parent::__construct(
            $this->constructHydrator(),
            $this->constructModelPrototype(),
            $this->constructCollectionPrototype()    
        );
    }
    
    protected function constructHydrator()
    {
        return new \Core\Repository\MongoDb\Hydrator\ModelHydrator();
    }
    
    abstract protected function constructModelPrototype();
    
    protected function constructCollectionPrototype()
    {
        return new \Core\Model\Collection();
    }
}