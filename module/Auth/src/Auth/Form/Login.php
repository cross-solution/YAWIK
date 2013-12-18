<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Auth\Form;

use Core\Form\Form;
use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class Login extends Form 
{
    
	public function init()
    {
        $this->setName('login-form');
             
        
        $fieldset = new Fieldset('credentials');
        //$fieldset->setLabel('Enter your credentials');
        $fieldset->setOptions(array('renderFieldset' => true));
        $fieldset->add(array(
            'name' => 'login',
            'options' => array(
                'label' => /*translate*/ 'Login name',
            ),
        ));
        
        $fieldset->add(array(
            'type' => 'password',
            'name' => 'credential',
            'options' => array(
                'label' => /*translate*/ 'Password',
                
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
                'value' => 'login',
                'class' => 'btn btn-primary'
            ),
        ));
        
        $this->add($buttons);
    }
    
    
}