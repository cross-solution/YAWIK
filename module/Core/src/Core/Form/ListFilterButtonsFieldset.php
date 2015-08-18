<?php

namespace Core\Form;

class ListFilterButtonsFieldset extends ButtonsFieldset
{
    public function init()
    {
        $this->setName('buttons');
        //$this->setLabel('Actions');
        $this->add(
            array(
            'type' => 'Button',
            'name' => 'submit',
            'options' => array(
                'label' => /*@translate*/ 'Apply filter',
            ),
            'attributes' => array(
                'id' => 'submit',
                'type' => 'submit',
                'value' => 'Apply filter',
                'class' => 'btn btn-primary'
            ),
            )
        );
        
        $this->add(
            array(
            'type' => 'Button',
            'name' => 'cancel',
            'options' => array(
                'label' => /*@translate*/ 'Reset filter',
            ),
            'attributes' => array(
                'id' => 'cancel',
                'type' => 'reset',
                'value' => 'Reset filter',
                'class' => 'btn btn-secondary'
            ),
            )
        );
    }
}
