<?php

namespace Core\Mapper\Query\Option;

use Zend\Stdlib\AbstractOptions;

abstract class AbstractOption extends AbstractOptions implements OptionInterface
{
    protected $paramsMap = array();
    
    public function __construct(array $params=array())
    {
        parent::__construct();
        $this->setParams($params);
    }
    
    public function setParams(array $params=array())
    {
        foreach ($params as $index => $value) {
            if (isset($this->paramsMap[$index])) {
                $this->__set($this->paramsMap[$index], $value);
            }   
        }
        return $this;
    }
}