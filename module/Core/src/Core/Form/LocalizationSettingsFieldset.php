<?php

namespace Core\Form;

use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class LocalizationSettingsFieldset extends Fieldset
{
    
   
    
	public function init()
    {
        $this->setLabel('general settings');
        
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