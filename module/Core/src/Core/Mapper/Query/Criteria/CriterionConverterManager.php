<?php

namespace Core\Mapper\Query\Criteria;

use Zend\ServiceManager\AbstractPluginManager;

class CriterionConverterManager extends AbstractPluginManager
{
    
    public function validatePlugin($plugin)
    {
        if ($plugin instanceOf Criterion\CriterionConverterInterface) {
            return;
        }
        
        die ("Error handling not implemented.");
    }
    
}