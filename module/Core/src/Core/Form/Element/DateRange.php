<?php

namespace Core\Form\Element;

use Zend\Form\Element;
use Zend\Form\Element\Date;
use Zend\Form\Element\Checkbox;

class DateRange extends Element
{
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        
        $this->startDateElement = new Date('start');
        $this->endDateElement   = new Date('end');
        $this->currentCheckbox  = new Checkbox('current'); 
    }
    
    
}