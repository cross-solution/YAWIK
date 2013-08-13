<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\Employment as EmploymentEntity;
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
            'type' => 'DateSelect',
            'name' => 'startDate',
            'options' => array(
                'label' => 'Start date'
            )
        ));
        $this->add(array(
            'type' => 'DateSelect',
            'name' => 'endDate',
            'options' => array(
                'label' => 'End date'
            )
        ));
               
    }
    
}