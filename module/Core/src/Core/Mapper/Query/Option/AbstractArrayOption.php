<?php

namespace Core\Mapper\Query\Option;

/**
 * @todo implement IteratorInterface or ArrayAccess
 */
class AbstractArrayOption implements ArrayOptionInterface
{
    
    protected $collection = array();
    protected $itemClass;
    
    public function setParams(array $params=array())
    {
        $item = $this->getItem($params);
        $this->collection[] = $item;
        return $this;
    }

    public function getCollection()
    {
        return $this->collection;
    }
    
    protected function getItem($params)
    {
        $item = new $this->itemClass($params);
        return $item;
    }
}