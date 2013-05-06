<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\LanguageSkill as LanguageSkillModel;
use Core\Model\Hydrator\ModelHydrator;

class LanguageSkillFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('languageskill')
             ->setHydrator(new ModelHydrator())
             ->setObject(new LanguageSkillModel())
             ->setLabel('Lanuageskill');
        
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
        
        $this->add(array(
            'name' => 'level',
        	'type' => 'Zend\Form\Element\Select',
        	'options' => array(
       				'label' => 'level',
        			'value_options' => array(
        					'a1' => /*@translate */ 'I can use simple phrases and sentences to describe where I live and people I know.',
        					'a2' => /*@translate */ 'I can use a series of phrases and sentences to describe in simple terms my family and other people, living conditions, my educational background and my present or  most recent job.',
        					'b1' => /*@translate */ 'I can connect phrases in a simple way in order to describe experiences and events, my dreams, hopes and ambitions. I can briefly give reasons and explanations for opinions and plans. I can narrate a story or relate the plot of a book or film and describe my reactions.',
        					'b2' => /*@translate */ 'I can present clear, detailed descriptions on a wide range of subjects related to my field of interest. I can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options.',
        					'c1' => /*@translate */ 'I can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion.',
        					'c2' => /*@translate */ 'I can present a clear, smoothly-flowing description or argument in a style appropriate to the context and with an effective logical structure which helps the recipient to notice and remember significant points.',
        			)
        	),
        	'attributes' => array(
        			'id' => 'languageskill-level',
        			'title' => /*@translate */ 'level'
        	)
        		
        ));
               
    }
    
}