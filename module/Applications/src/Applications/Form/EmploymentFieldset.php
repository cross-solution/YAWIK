<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Entity\Employment as EmploymentEntity;
use Core\Entity\Hydrator\EntityHydrator;

class EmploymentFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('employment')
             ->setHydrator(new EntityHydrator())
             ->setObject(new EmploymentEntity())
             ->setLabel('Employment');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));
        
        $this->add(array(
            'name' => 'range',
            'type' => '\Core\Form\Element\DateRange',
            'options' => array(
                
            ),
        ));
        
               
    }
    
}