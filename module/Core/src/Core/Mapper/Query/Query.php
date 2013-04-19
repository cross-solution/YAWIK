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
            return $this->__get($match[1]);
        }
        $optionManager = $this->getOptionManager();
        if ($optionManager->has($method)) {
            $option = $optionManager->get($method);
            $option->setParams($params);
            $this->options[strtolower($method)] = $option; 
            return $this;
        }
        die ('Unsupported option: ' . $method);
    }
    
    public function __get($name)
    {
        $option = strtolower($name);
        return isset($this->options[$option]) ? $this->options[$option] : null;
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