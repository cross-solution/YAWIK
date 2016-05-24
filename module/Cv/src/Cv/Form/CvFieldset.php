<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\Strategy\CollectionStrategy;

class CvFieldset extends Fieldset
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $collectionStrategy = new CollectionStrategy();
            $hydrator->addStrategy('educations', $collectionStrategy)
                     ->addStrategy('employments', $collectionStrategy)
                     ->addStrategy('skills', $collectionStrategy);
                      
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('cv');
        $this->setAttribute('id', 'cv');
        $this->add(
            array(
            'type' => 'hidden',
            'name' => 'id'
            )
        );
        
        $this->add([
            'type' => 'Collection',
            'name' => 'employments',
            'options' => [
                'label' => /*@translate */ 'Employment history',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => true,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'EmploymentFieldset'
                ]
            ]
        ]);
        
        $this->add([
            'type' => 'EducationCollection'
        ]);
        
        $this->add([
            'type' => 'Collection',
            'name' => 'skills',
            'options' => [
                'label' => /*@translate */ 'Skills',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => true,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'SkillFieldset'
                ]
            ]
        ]);
    }
}
