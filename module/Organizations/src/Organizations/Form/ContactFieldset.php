<?php

namespace Organizations\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class ContactFieldset extends Fieldset 
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $fieldset = new Fieldset('contact');
        $fieldset->setHydrator(new EntityHydrator());
        
        $this->setName('organization');
        
        $fieldset->add(array(
        		'name' => 'street',
        		'options' => array(
        				'label' => /* @translate */ 'street'
        		)
        ));
        
        $fieldset->add(array(
        		'name' => 'housenumber',
        		'options' => array(
        				'label' => /* @translate */ 'house number'
        		)
        ));
        
        $fieldset->add(array(
        		'name' => 'postalcode',
        		'options' => array(
        				'label' => /* @translate */ 'Postalcode'
        		)
        ));
        
        $fieldset->add(array(
        		'name' => 'city',
        		'options' => array(
        				'label' => /* @translate */ 'City'
        		)
        ));
        
        $this->add($fieldset);
        
    }
    
    public function getInputFilterSpecification()
    {
        return array();
    }
    
    /*
    public function setObject($object)
    {
        parent::setObject($object->contact);
        $this->populateValues($this->extract());
        return $this;
    }
     */
}