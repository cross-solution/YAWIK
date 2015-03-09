<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Form;

use Core\Form\Form;
use Zend\Form\Fieldset;

class Login extends Form 
{
	public function __construct($name = 'login-form', $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('data-handle-by', 'native');
        $this->setAttribute('class', 'form-inline');
             
        
        $fieldset = new Fieldset('credentials');
        $fieldset->setOptions(array('renderFieldset' => true));
        $fieldset->add(array(
            'name' => 'credentials[login]',
            'options' => array(
                'id' => 'credentials-login',
                'label' => /* @translate */ 'Login name',
            ),
        ));
        
        $fieldset->add(array(
            'type' => 'password',
            'name' => 'credentials[credential]',
            'options' => array(
                'id' => 'credentials-credential',
                'label' => /* @translate */ 'Password',
                
            ),
        ));

        $this->add($fieldset);
            
        $buttons = new \Core\Form\ButtonsFieldset('buttons');
        $buttons->add(array(
            'type' => 'submit',
            'name' => 'button',
            'attributes' => array(
                'id' => 'submit',
                'type' => 'submit',
                'value' => /* @translate */ 'login',
                'class' => 'btn btn-primary'
            ),
        ));

        $this->add($buttons);
    }
}
