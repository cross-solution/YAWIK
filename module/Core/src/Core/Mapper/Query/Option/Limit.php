<?php

namespace Core\Mapper\Query\Option;

class Limit extends AbstractOption
{
    protected $paramNamesMap = array(
        'count',
        'offset',
    );
    
    protected $count;
    protected $offset;

    
    /**
     * @return the $count
     */
    public function getCount ()
    {
        return $this->count;
    }

	/**
     * @param field_type $count
     */
    public function setCount ($count)
    {
        $this->count = $count;
        return $this;
    }

	/**
     * @return the $offset
     */
    public function getOffset ()
    {
        return $this->offset;
    }

	/**
     * @param field_type $offset
     */
    public function setOffset ($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    
    
}