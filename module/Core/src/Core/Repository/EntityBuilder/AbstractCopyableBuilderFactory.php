<?php

namespace Core\Repository\EntityBuilder;

abstract class AbstractCopyableBuilderFactory
{
    
    protected function getBuilderName($builderName)
    {
        return $builderName;
    }
    
    protected function getBuilderClass()
    {
        return __NAMESPACE__ . '\\EntityBuilder';
    }
    
    protected function getHydrator()
    {
        return new \Core\Repository\Hydrator\EntityHydrator();
    }
}