<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\Skill as SkillEntity;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\Strategy\CollectionStrategy;

class SkillFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $collectionStrategy = new CollectionStrategy();
            $hydrator->addStrategy('nativeLanguages', $collectionStrategy)
                ->addStrategy('languageSkills', $collectionStrategy);
            
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('skill')
            ->setLabel('Languages')
            ->setObject(new SkillEntity());
        
        $this->add(array(
            'type' => 'Collection',
            'name' => 'nativeLanguages',
            'options' => array(
                'label' => /*@translate */ 'Native Language',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Cv/NativeLanguageFieldset'
                )
            )
        ));
        
        $this->add(array(
            'type' => 'Collection',
            'name' => 'languageSkills',
            'options' => array(
                'label' => /*@translate */ 'Other languages',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'LanguageSkillFieldset'
                )
            )
        ));
    }
}
