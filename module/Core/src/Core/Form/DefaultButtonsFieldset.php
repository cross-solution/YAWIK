<?php

namespace Core\Form;

use Zend\Form\Fieldset;

class DefaultButtonsFieldset extends Fieldset
{
    
    public function init()
    {
        $this->setName('buttons');
        //$this->setLabel('Actions');
        $this->add(array(
            'type' => 'Button',
            'name' => 'submit',
            'options' => array(
                'label' => /*@translate*/ 'Save',
            ),
            'attributes' => array(
                'id' => 'submit',
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}