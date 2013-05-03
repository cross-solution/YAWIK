<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface EducationInterface extends ModelInterface
{
    public function setName($name);
    public function getName();
    
    public function setValue($value);
    public function getValue();
    
    
    
}