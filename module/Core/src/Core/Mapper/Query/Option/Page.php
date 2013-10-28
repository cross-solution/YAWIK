<?php

namespace Core\Mapper\Query\Option;

class Page extends Limit
{
    protected $optionName = 'limit';
    
    protected $paramNamesMap = array(
        'count',
        'page',
    );
    
    public function setFromParams(array $params=array()) 
    {
        return parent::setFromParams(array_reverse($params));
    }
        
    public function setPage($page)
    {
        $offset = ($page - 1) * $this->getCount();
        return $this->setOffset($offset);
    }
    
}