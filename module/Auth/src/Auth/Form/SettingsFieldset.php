<?php

namespace Auth\Form;

use Core\Entity\Hydrator\AnonymEntityHydrator;
use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset
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
        $this->setName('settings-core-fieldset')
             ->setLabel('general settings');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailAccess',
        		'options' => array(
        				'label' => /* @translate */ 'receive E-Mail alert',
        				),
        		)
        );
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailText',
        		'options' => array(
        				'label' => /* @translate */ 'Mailtext',
        				),
        		)
        );
    }
    
    
}