<?php

namespace Jobs\Filter;

use Zend\Filter\FilterInterface;

class ParamsToProperties implements FilterInterface
{
    protected $auth;

    public function __construct($auth)
    {
        
        $this->auth = $auth;
    }
    
    public function filter($value)
    {
        $properties = array();
        
        if (isset($value['sort'])) {
            $properties['sort'] = $this->filterSort($value['sort']);
        }
         
        
        if (isset($value['by']) && 'me' == $value['by']) {
            $properties['userId'] = $this->auth->getUser()->id;
        } 
        
        
        return $properties;
    }
    
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        switch ($sortProp) {
            case "date":
                $sortProp = "datePublishStart.date";
                break;
                
            default:
                break;
        }
        
        return array($sortProp => $sortDir);
        
    }
}