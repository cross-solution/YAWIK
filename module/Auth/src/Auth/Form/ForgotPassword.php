<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Core\Form\ButtonsFieldset;
use Core\Form\Form;

class ForgotPassword extends Form
{
    public function __construct($name = 'forgot-password', $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('data-handle-by', 'native');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            array(
                'type' => 'text',
                'name' => 'identity',
                'options' => array(
                    'label' => /* @translate */ 'Username or email',
                    'is_disable_capable' => false,
                ),
            )
        );

        $buttons = new ButtonsFieldset('buttons');
        $buttons->add(
            array(
                'type' => 'submit',
                'name' => 'button',
                'attributes' => array(
                    'id' => 'submit',
                    'type' => 'submit',
                    'value' => /* @translate */ 'Reset your password',
                    'class' => 'btn btn-primary'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'csrf',
                'type' => 'csrf',
            )
        );

        $this->add($buttons);
    }
}
