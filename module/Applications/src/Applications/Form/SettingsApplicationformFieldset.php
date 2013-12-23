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
        		'name' => 'formDisplaySkills',
        		'options' => array('label' => /* @translate */ 'display skills')));
        
    }
}