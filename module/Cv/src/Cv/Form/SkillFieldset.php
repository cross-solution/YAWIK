<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\Skill as SkillEntity;
use Core\Entity\Hydrator\EntityHydrator;

class SkillFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('skill')
             ->setHydrator(new EntityHydrator())
             ->setObject(new SkillEntity())
             ->setLabel('Language');
        
        $this->add(
            array(
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
            )
        );
        
        
        $this->add(
            array(
                'type' => 'Collection',
                'name' => 'languageskill',
                'options' => array(
                        'label' => /*@translate */ 'Other languages',
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
            )
        );
        
              
    }
}
