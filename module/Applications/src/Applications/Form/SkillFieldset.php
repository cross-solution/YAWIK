<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Skill as SkillModel;
use Core\Model\Hydrator\ModelHydrator;

class SkillFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('skill')
             ->setHydrator(new ModelHydrator())
             ->setObject(new SkillModel())
             ->setLabel('Skills');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));
        
        $this->add(array(
        		'type' => 'Collection',
        		'name' => 'nativeLanguage',
        		'options' => array(
        				'label' => /*@translate */ 'Native Language',
        				'count' => 1,
        				'should_create_template' => true,
        				'allow_add' => true,
        				'target_element' => array(
        						'type' => 'NativeLanguageFieldset'
        				)
        		),
        		'attributes' => array(
        				'id' => 'skill'
        		),
        ));
        
        
        $this->add(array(
        		'type' => 'Collection',
        		'name' => 'languageskill',
        		'options' => array(
        				'label' => /*@translate */ 'LanguageSkills',
        				'count' => 1,
        				'should_create_template' => true,
        				'allow_add' => true,
        				'target_element' => array(
        						'type' => 'LanguageSkillFieldset'
        				)
        		),
        		'attributes' => array(
        				'id' => 'skill'
        		),
        ));
        
              
    }
    
}