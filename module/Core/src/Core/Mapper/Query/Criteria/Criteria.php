<?php

namespace Core\Mapper\Query\Criteria;

use Zend\ServiceManager\AbstractPluginManager;
class Criteria implements CriteriaInterface
{
    protected $criterionPluginManager;
    protected $chainAnd = true;
    protected $criteria = array();
    
    public function __call($method, $params) {
        $criterion = $this->plugin($method, $params);
        return $this->add($criterion);
    }
    
    public function plugin($name, array $options=array())
    {
        return $this->getCriterionPluginManager()->get($name, $options);
    }
    
    public function chainOr()
    {
        $this->chainAnd = false;
        return $this;
    }
    
    public function isChainAnd() 
    {
        return $this->chainAnd;
    }
    
    public function isChainOr()
    {
        return !$this->chainAnd;
    }
    
    public function chainAnd()
    {
        $this->chainAnd = true;
        return $this;
    }
    
    public function criteria(CriteriaInterface $criteria)
    {
        $this->criteria[] = $criteria;
        return $this;
    }
    
    public function add (Criterion\CriterionInterface $criterion)
    {
        $this->criteria[] = $criterion;
        return $this;
    }
    
    public function getCriteria()
    {
        return $this->criteria;
    }

    public function setCriterionPluginManager (AbstractPluginManager $pluginManager)
    {
        $this->criterionPluginManager = $pluginManager;
        return $this;
    }
    
    public function getCriterionPluginManager()
    {
        if (!$this->criterionPluginManager) {
            $this->criterionPluginManager = new CriterionManager();
        }
        return $this->criterionPluginManager;
    }
}