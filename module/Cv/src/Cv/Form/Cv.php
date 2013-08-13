<?php

namespace Cv\Form;

use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

class Cv extends Form
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $arrayToCollectionStrategy = new ArrayToCollectionStrategy();
            $hydrator->addStrategy('educations', $arrayToCollectionStrategy)
                     ->addStrategy('employments', $arrayToCollectionStrategy);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('cv-create');
        $this->setAttribute('id', 'cv-create');
        
        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));
        
        $this->add(array(
            'type' => 'Collection',
            'name' => 'educations',
            'options' => array(
                'label' => /*@translate */ 'Education history',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => false,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'EducationFieldset'
                )
            ),
            'attributes' => array(
                //'id' => 'educations'
            ),
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
            'type' => 'DefaultButtonsFieldset'
        ));
    }
}