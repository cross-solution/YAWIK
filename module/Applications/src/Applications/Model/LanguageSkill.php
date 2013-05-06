<?php

namespace Applications\Model;

use Core\Model\AbstractModel;

class LanguageSkill extends AbstractModel
{
    
    protected $name;
    protected $value;
    
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}