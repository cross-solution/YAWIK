<?php

namespace Applications\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Form;
use Zend\Form\Fieldset;

class CreateApplication extends Form //implements ServiceLocatorAwareInterface
{
    
	public function init()
    {
        $this->setName('create-application-form');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());
        
        $this->add(array(
            'type' => 'ApplicationFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
                'label' => 'Contact details',
            ),
            'attributes' => array(
                'id' => 'application'
            )
        ));
        
        $buttons = new Fieldset('buttons');
        $buttons->add(array(
            'type' => 'Button',
            'name' => 'submit',
            'options' => array(
                'label' => 'Apply'
            ),
            'attributes' => array(
                'id' => 'submit',
                'type' => 'submit',
                'value' => 'Save',
            )
        ));
        
        $this->add($buttons);
    }
}