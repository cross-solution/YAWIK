<?php

namespace Core\Mapper\Query;

use Core\Mapper\Query\Criteria\CriteriaInterface;
use Zend\ServiceManager\Config;
use Core\Mapper\MapperInterface;

abstract class AbstractConverter
{
    protected $criterionConverterClasses = array();
    protected $criterionConverterPluginManager;
    
    
    
    public function convert(Query $query, MapperInterface $mapper)
    {
        
    }
    
    public function setCriterionConverterPluginManager( Criteria\CriterionConverterManager $manager)
    {
        $this->criterionConverterPluginManager = $manager;
        return $this;
    }
    
    public function getCriterionConverterPluginManager()
    {
        if (!$this->criterionConverterPluginManager) {
             $config = new Config(array(
                 'invokables' => $this->criterionConverterClasses,
             ));
             $pm = new Criteria\CriterionConverterManager($config);
             $this->criterionConverterPluginManager = $pm;
        }
        return $this->criterionConverterPluginManager;
    }
    
    
}