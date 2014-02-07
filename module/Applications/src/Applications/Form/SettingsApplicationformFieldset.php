<?php

namespace Applications\Form;

use Core\Entity\Hydrator\AnonymEntityHydrator;
use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsApplicationformFieldset extends Fieldset
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new AnonymEntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

public function init()
    {
    
        $this->setName('applicationForm')
             ->setLabel(/* @translate */ 'applicationform');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());
        
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'formDisplaySummary',
        		'options' => array('label' => /* @translate */ 'Hide Summary')));
          
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'formDisplayEducationHistory',
        		'options' => array('label' => /* @translate */ 'Hide Education history')));
          
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'formDisplayEmploymentHistory',
        		'options' => array('label' => /* @translate */ 'Hide Employment history')));
          
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'formDisplaySkills',
        		'options' => array('label' => /* @translate */ 'Hide Skills')));
          
          
        
    }
}