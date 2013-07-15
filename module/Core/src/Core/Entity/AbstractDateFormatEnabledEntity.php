<?php

namespace Core\Entity;

abstract class AbstractDateFormatEnabledEntity extends AbstractEntity implements DateFormatEnabledInterface
{
    
    public function __get($name)
    {
        if (preg_match('~(.*)Date$~', $name, $match)) {
            return $this->getFormattedDate("date" . $match[1]);
        }
        return parent::__get($name);
    }
    
    /**
     * 
     * @param unknown $method
     * @param unknown $params
     */
    public function __call($method, $params)
    {
       if (preg_match('~^(.*)Date$~', $method, $match)) {
           return $this->getFormattedDate("date" . $match[1], isset($params[0]) ? $params[0] : null);
       }

       //@todo error handling
       trigger_error("Unknown method '$method' called on '" . get_class($this) . "'", E_USER_ERROR);
    } 
    
    public function getFormattedDate($property, $format="%x")
    {
        $value = $this->__get($property);
        
        if (!$value instanceOf \DateTime) {
            //@todo Error handling
            return null;
        }
        
        return strftime($format, $value->getTimestamp());
    }
}