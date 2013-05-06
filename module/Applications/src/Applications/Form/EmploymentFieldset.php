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
        		'name' => 'startDate',
        		'type' => 'Zend\Form\Element\Date',
        		'options' => array(
        				'label' => /*@translate */ 'Startdate'
        		),
        		'attributes' => array(
        				'id' => 'employment-startdate'
        		)
        ));
        
        $this->add(array(
        		'name' => 'endDate',
        		'type' => 'Zend\Form\Element\Date',
        		'options' => array(
        				'label' => /*@translate */ 'Enddate'),
        		'attributes' => array(
        				'id' => 'employment-enddate',
        		),
        ));
        
        $this->add(array(
        		'name' => 'currentIndicator',
        		'type' => 'Zend\Form\Element\Checkbox',
        		'options' => array(
        				'label' => /*@translate */ 'Current'
        		),
        		'attributes' => array(
        				'id' => 'employment-currentindicator'
        		)
        ));
               
    }
    
}