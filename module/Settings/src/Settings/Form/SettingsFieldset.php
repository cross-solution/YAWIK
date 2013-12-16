<?php

namespace Settings\Form;

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
        		'type' => 'Zend\Form\Element\Select',
        		'name' => 'language',
        		'options' => array(
        				'label' => /* @translate */ 'choose your language',
        				'value_options' => array(
        						'en' => /* @translate */ 'English',
        						'fr' => /* @translate */ 'French',
        						'de' => /* @translate */ 'German',
        				),
                                        'description' => /* @translate */ 'defines the languages of this frontend.'
        		),
        ));
        
        
        
    }
    
    
}