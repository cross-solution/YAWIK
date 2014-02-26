<?php

namespace Jobs\Form;

use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;

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
        $this->setName('jobs-form');
        $this->setAttribute('id', 'jobs-form');
 
        
        $this->add(array(
            'type' => 'Jobs/JobFieldset',
            'name' => 'job',
            'options' => array(
                'use_as_base_fieldset' => true
            ),
        ));       
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset',
            'options' => array(
                'save_label' => /*@translate*/ 'Publish job',
            ),
        ));
        

    }
}