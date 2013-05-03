<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Education as EducationModel;
use Core\Model\Hydrator\ModelHydrator;

class EducationFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('education')
             ->setHydrator(new ModelHydrator())
             ->setObject(new EducationModel())
             ->setLabel('Education');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
            'attributes' => array(
                'id' => 'education-id'
            ),
        ));
        
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name'
            ),
            'attributes' => array(
                'id' => 'education-name'
            )
        ));
        
        $this->add(array(
            'name' => 'value',
            'attributes' => array(
                'id' => 'education-value'
            ),
        ));
               
    }
    
}