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
        
        $this->add(
            array(
            'type' => 'EducationCollection',
            )
        );
        
        $this->add(
            array(
            'type' => 'Collection',
            'name' => 'employments',
            'options' => array(
                'label' => /*@translate */ 'Employment history',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'EmploymentFieldset'
                )
            ),
            'attributes' => array(
                //'id' => 'educations'
            ),
            )
        );
        
        $this->add(
            array(
                'type' => 'Collection',
                'name' => 'skills',
                'options' => array(
                        'label' => /*@translate */ 'Skills',
                        'count' => 0,
                        'should_create_template' => true,
                        'use_labeled_items' => false,
                        'collapsable' => true,
                        'collapsed' => true,
                        'allow_add' => true,
                        'target_element' => array(
                                'type' => 'SkillFieldset'
                        )
                ),
                'attributes' => array(
                        //'id' => 'educations'
                ),
            )
        );
    }
}
