<?php

namespace Core\Mapper\Query\Option;

class Page extends Limit
{
    
    protected $paramsMap = array(
        'page',
        'count',
    );
    
    public function setParams(array $params=array()) 
    {
        return parent::setParams(array_reverse($params));
    }
        
    public function setPage($page)
    {
        $offset = ($page - 1) * $this->getCount();
        return $this->setOffset($offset);
    }
    
}