<?php

namespace Core\Mapper\Criteria;

class Criterion implements CriterionInterface
{
    const MODE_EQUALS = "EQUALS";
    
    protected $property;
    protected $value;
    protected $mode;
    
    public function __construct($property, $value, $mode=self::MODE_EQUALS)
    {
        $this->property = $property;
        $this->value = $value;
        $this->mode = $mode;
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

	/**
     * @return the $mode
     */
    public function getMode ()
    {
        return $this->mode;
    }

	/**
     * @param field_type $mode
     */
    public function setMode ($mode)
    {
        $this->mode = $mode;
    }

    
    
         
}