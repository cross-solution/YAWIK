<?php

namespace Core\Mapper\Query\Option;

use Zend\Stdlib\AbstractOptions;

abstract class AbstractOption extends AbstractOptions implements OptionInterface
{
    protected $optionName;
    protected $paramNamesMap = array();

    
    public function setFromParams(array $params)
    {
        $values = array();
        foreach ($params as $key => $val) {
            if (isset($this->paramNamesMap[$key])) {
                $values[$this->paramNamesMap[$key]] = $val;
            }
        }
        //$values = array_combine($this->paramNamesMap, $params);
        return $this->setFromArray($values);
    }
    
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;
        return $this;
    }
    
    public function getOptionName()
    {
        if (!$this->optionName) {
            $name = get_class($this);
            $name = explode('\\', $name);
            $name = array_pop($name);
            $this->optionName = $name;
        }
        return $this->optionName;
    }
}