<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

class ListFilterButtonsFieldset extends ButtonsFieldset
{
    /**
     * Initialize the list filter buttons
     */
    public function init()
    {
        $this->setName('buttons');

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
