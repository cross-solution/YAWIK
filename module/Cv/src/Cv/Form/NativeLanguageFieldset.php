<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\NativeLanguage as NativeLanguageEntity;
use Core\Entity\Hydrator\EntityHydrator;

class NativeLanguageFieldset extends Fieldset
{
    public function init()
    {
    	$this->setName('nativeLanguage')
	    	->setHydrator(new EntityHydrator())
    		->setObject(new NativeLanguageEntity())
    		->setLabel('Native Language');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));
        
        $this->add(array(
            'name' => 'language',
        	'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Language',
            	'value_options' => array(
            			'fr' => 'French',
            			'en' => 'English',
            			'jp' => 'Japanese',
            			'cn' => 'Chinese',
            			)
            ),
        	'attributes' => array(
        				'id' => 'languageskill-language',
        				'title' => /*@translate */ 'which language are you speeking'
        	)
        ));
        
               
    }
    
}