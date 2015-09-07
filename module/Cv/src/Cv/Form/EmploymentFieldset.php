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
        
        $this->add(
            array(
            'type' => 'DateSelect',
            'name' => 'startDate',
            'options' => array(
                'label' => /*@translate */ 'Start date'
            )
            )
        );
        $this->add(
            array(
            'type' => 'DateSelect',
            'name' => 'endDate',
            'options' => array(
                'label' => /*@translate */ 'End date'
            )
            )
        );
        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'currentIndicator',
                'options' => array(
                        'label' => /*@translate */ 'ongoing'
                )
            )
        );
        $this->add(
            array(
                'name' => 'organizationName',
                'options' => array(
                        'label' => /*@translate */ 'Company Name'),
                'attributes' => array(
                        'title' =>  /*@translate */ 'please enter the name of the company'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'description',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                        'label' => /*@translate */ 'Description',
                ),
                'attributes' => array(
                        'title' => /*@translate */ 'please describe your position',
                ),
            )
        );
        
               
    }
}
