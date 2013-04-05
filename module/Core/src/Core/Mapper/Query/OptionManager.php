<?php

namespace Core\Mapper\Query;

use Zend\ServiceManager\AbstractPluginManager;
use Core\Mapper\Query\Option\OptionInterface;

class OptionManager extends AbstractPluginManager
{
    protected $invokableClasses = array(
        'limit' => '\Core\Mapper\Query\Option\Limit',
        'page' => '\Core\Mapper\Query\Option\Page',
        'sort' => '\Core\Mapper\Query\Option\Sort',
    );
    
    public function validatePlugin($plugin)
    {
        if ($plugin instanceOf OptionInterface) {
            return;
        }
        
        die ("Error handling ndot implemented.");
    }
    
}