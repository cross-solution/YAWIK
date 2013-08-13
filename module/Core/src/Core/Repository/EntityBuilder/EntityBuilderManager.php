<?php

namespace Core\Repository\EntityBuilder;

use Zend\ServiceManager\AbstractPluginManager;


class EntityBuilderManager extends AbstractPluginManager
{
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf EntityBuilderInterface) {
            die (__METHOD__. ': Plugin must implement EntityBuilderInterface');
        }
        
        
    }
}