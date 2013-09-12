<?php

namespace Settings\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset
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
        $this->setName('settings-core-fieldset')
             ->setLabel('general settings');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Radio',
        		'name' => 'language',
        		'options' => array(
        				'label' => /* @translate */ 'choose your language',
        				'value_options' => array(
        						'en' => /* @translate */ 'English',
        						'de' => /* @translate */ 'German',
        				),
        		),
        ));
        
        
        
    }
    
    
}