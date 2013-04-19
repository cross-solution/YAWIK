<?php

namespace Core\Mapper\Query\Criteria\Criterion;

abstract class AbstractCriterion implements CriterionInterface
{
    
    protected $property;
    protected $value;
    
    public function __construct(array $params=array())
    {
        if (2 > count($params)) {
            die (__METHOD__ . " >> Too few arguments");
        }
        $this->property = $params[0];
        $this->value = $params[1];
    }
    
	/**
     * @return the $property
     */
    public function getProperty ()
    {
        return $this->property;
    }

	/**
     * @param field_type $property
     */
    public function setProperty ($property)
    {
        $this->property = $property;
    }

	/**
     * @return the $value
     */
    public function getValue ()
    {
        return $this->value;
    }

	/**
     * @param field_type $value
     */
    public function setValue ($value)
    {
        $this->value = $value;
    }

}