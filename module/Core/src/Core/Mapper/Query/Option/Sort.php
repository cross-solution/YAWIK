<?php

namespace Core\Mapper\Query\Option;


class Sort extends AbstractArrayOption
{
    protected $paramNamesMap = array(
        'property',
        'ascending',
    );
    
    protected $property;
    protected $ascending = true;
   
    
    public function setProperty($property) {
        $this->property = $property;
        return $this;
    }
    
    public function getProperty()
    {
        return $this->property;
    }
    
    public function setAscending($flag)
    {
        $this->ascending = false;
        return $this;
    }
    
    public function getAscending()
    {
        return $this->ascending;
    }
    
    public function isAscending()
    {
        return $this->getAscending();
    }
    
    public function isDescending()
    {
        return !$this->getAscending();
    }
    
}