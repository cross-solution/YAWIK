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
            'type' => 'CvFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            ),
        ));       
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));

    }
}