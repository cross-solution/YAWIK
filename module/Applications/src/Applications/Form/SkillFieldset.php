<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Skill as SkillModel;
use Core\Model\Hydrator\ModelHydrator;

class SkillFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('skill')
             ->setHydrator(new ModelHydrator())
             ->setObject(new SkillModel())
             ->setLabel('Skill');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));
        
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name'
            )
        ));
        
        $this->add(array(
            'name' => 'value',
        ));
               
    }
    
}