<?php

namespace Core\Mapper\Query\Option;


class AbstractArrayOption extends AbstractOption
{
    
    protected $__collection = array();
    
    public function setFromParams(array $params, $calledBySelf=false) {
        if (is_array($params[0])) {
            foreach ($params[0] as $paramArray) {
                $this->setFromParams($paramArray);
            }
            return $this;
        }
        
        if ($calledBySelf) {
            return parent::setFromParams($params);
        } else {
            $item = new static();
            $item->setFromParams($params, true);
            $this->__collection[] = $item;
            return $this;
        }
    }
    
    public function getCollection()
    {
        return $this->__collection;
    }
        
       
}