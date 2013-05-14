<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Employment as EmploymentModel;
use Core\Model\Hydrator\ModelHydrator;

class EmploymentFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('employment')
             ->setHydrator(new ModelHydrator())
             ->setObject(new EmploymentModel())
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