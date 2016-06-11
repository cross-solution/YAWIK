<?php

namespace Cv\Form;

use Cv\Entity\PreferredJob;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class PreferredJobFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('preferredJob')
             ->setLabel('Desired Employment');
        

        $this->add(
            array(
                'name' => 'desiredJob',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                        'label' => /*@translate */ 'Description',
                ),
                'attributes' => array(
                        'title' => /*@translate */ 'please describe your position',
                ),
            )
        );
        
               
    }
}
