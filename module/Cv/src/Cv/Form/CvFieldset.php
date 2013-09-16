<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

class CvFieldset extends Fieldset
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $arrayToCollectionStrategy = new ArrayToCollectionStrategy();
            $hydrator->addStrategy('educations', $arrayToCollectionStrategy)
                     ->addStrategy('employments', $arrayToCollectionStrategy)
                     ->addStrategy('skills', $arrayToCollectionStrategy);
                      
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('cv');
        $this->setAttribute('id', 'cv');
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));
        
        $this->add(array(
            'type' => 'EducationCollection',
        ));
        
        $this->add(array(
            'type' => 'Collection',
            'name' => 'employments',
            'options' => array(
                'label' => /*@translate */ 'Employment history',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => false,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'EmploymentFieldset'
                )
            ),
            'attributes' => array(
                //'id' => 'educations'
            ),
        ));
        
        $this->add(array(
        		'type' => 'Collection',
        		'name' => 'skills',
        		'options' => array(
        				'label' => /*@translate */ 'Skills',
        				'count' => 0,
        				'should_create_template' => true,
        				'use_labeled_items' => false,
        				'collapsable' => true,
        				'collapsed' => false,
        				'allow_add' => true,
        				'target_element' => array(
        						'type' => 'SkillFieldset'
        				)
        		),
        		'attributes' => array(
        				//'id' => 'educations'
        		),
        ));
    }
}