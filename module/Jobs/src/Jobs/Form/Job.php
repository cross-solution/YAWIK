<?php

namespace Job\Form;

use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

class Job extends Form
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('job-create');
        $this->setAttribute('job', 'cv-create');
 
        
        $this->add(array(
            'type' => 'JobFieldset',
            'name' => 'job',
            'options' => array(
                'use_as_base_fieldset' => true
            ),
        ));       
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));

    }
}