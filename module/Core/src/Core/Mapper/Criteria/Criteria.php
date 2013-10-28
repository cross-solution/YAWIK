<?php

namespace Core\Mapper\Criteria;

class Criteria implements CriteriaInterface
{
    
    const MODE_AND = 'AND';
    const MODE_OR  = 'OR';
    
    protected $mode;
    protected $criterions = array();
    
    protected $and = array();
    protected $or = array();
    
    
    public function __construct($mode=self::MODE_AND)
    {
        $this->setMode($mode);
    }
    
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this; 
    }
    
    public function getMode()
    {
        return $this->mode;
    }
    public function __call($method, $params) {
        
        if (preg_match('~^(or)?(equals)$~', strtolower($method), $match)) {
            $mode = strtoupper($method);
            $criterion = new Criterion($params[0], $params[1], $mode);
            $call = "add" . $match[1];
            return $this->$call($criterion);
        }
        return null;
    }
    
    public function add (CriterionInterface $criterion)
    {
        $this->and[] = $criterion;
        return $this;
    }
    
    public function addOr(CriterionInterface $criterion)
    {
        $this->or[] = $criterion;
        return $this;
    }
    
    public function addCriteria (CriteriaInterface $criteria)
    {
        $this->and[] = $criteria;
        return $this;
    }
    
    public function addOrCriteria (CriteriaInterface $criteria)
    {
        $this->or[] = $criteria;
        return $this;
    }
    
    public function getCriteria ()
    {
        return $this->and;
    }
    
    public function getOrCriteria()
    {
        return $this->or;
    }
    
}