<?php

namespace Core\Mapper\Query;

use Core\Mapper\Query\Criteria\CriteriaInterface;
use Core\Mapper\Query\Criteria\Criteria;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Query implements ServiceManagerAwareInterface
{
    protected $serviceManager;
    protected $optionManager;
    protected $options = array();
    protected $condition;
    
    public function __call($method, $params)
    {
        if (preg_match('~get(.*)$~', $method, $match)) {
            return $this->getOption($match[1]);
        }
        
        return $this->setOption($method, $params);
    }
    
    public function getOption($name)
    {
        
        $optionName = strtolower($name);
        if (isset($this->options[$optionName])) {
            return $this->options[$optionName];
        }
        
        if ($this->getOptionManager()->has($name)) {
            return null;
        }
        
        //@todo Error-Handling!
        die ('Invalid option "' . $name . '"');
        
    }

    public function setOption($name, array $params=array())
    {
        
        if ($name instanceOf Option\OptionInterface
            || $name instanceOf Option\ArrayOptionInterface
        ) {
            $this->options[strtolower($name->getOptionName())] = $name;
            return $this; 
        }
        
        try {
            $option = $this->getOptionManager()->get($name);
        } catch (\Exception $e) {
            die ('Unknown option: "' . $name. '"');
        }
        
        $option->setFromParams($params);

        $optionName = strtolower($option->getOptionName());
        $this->options[$optionName] = $option;
        
        return $this;
    }
    
    public function __get($name)
    {
        return $this->getOption($name);
    }
    
    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }
    
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->serviceManager = new ServiceManager();
        }
        return $this->serviceManager;
    }
    
    public function getOptionManager()
    {
        if (!$this->optionManager) {
            $this->optionManager = $this->getServiceManager()->get('query_option_manager');
        }
        return $this->optionManager;
    }
    
    public function condition(CriteriaInterface $criteria)
    {
        $this->condition = $criteria;
        return $this;
    }
    
    public function getCondition()
    {
        return $this->condition;
    }
    
    public function criteria() {
        $sm = $this->getServiceManager();
        return $sm->get('criteria');
    }
    
   
    
    
}