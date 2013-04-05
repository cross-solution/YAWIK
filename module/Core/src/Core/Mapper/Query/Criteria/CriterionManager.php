<?php

namespace Core\Mapper\Query\Criteria;

use Zend\ServiceManager\AbstractPluginManager;

class CriterionManager extends AbstractPluginManager
{
    protected $shareByDefault = false;
    
    protected $invokableClasses = array(
        'equals' => '\Core\Mapper\Query\Criteria\Criterion\Equals',
        'startswith' => '\Core\Mapper\Query\Criteria\Criterion\StartsWith',
    );
    
    public function validatePlugin($plugin)
    {
        if ($plugin instanceOf Criterion\CriterionInterface) {
            return;
        }
        
        die ("Error handling not implemented.");
    }
    
}