<?php

namespace Auth\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\Form;
use Zend\Form\Fieldset;

class LoginFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
        $form = new Form(); 
        $form->setName('login-form');

        $form->add(array(
            'type' => 'hidden',
            'name' => 'ref',
        ));
        
        $fieldset = new Fieldset();
        
        $fieldset->add(array(
            'name' => 'login',
            'options' => array(
                'label' => /*translate*/ 'Login name',
                'description' => /*translate*/ 'Provide your login key (e.g. email adress)',
            ),
        ));
        
        $fieldset->add(array(
            'type' => 'password',
            'name' => 'credential',
            'options' => array(
                'label' => /*translate*/ 'Password',
            ),
        ));
        
        
        
        $form->add($fieldset);
        
        $form->add($this->forms->get('DefaultButtonsFieldset'));
        
    }
}