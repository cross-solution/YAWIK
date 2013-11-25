<?php

namespace Applications\Form;

use Core\Entity\Hydrator\AnonymEntityHydrator;
use Zend\Form\Fieldset;
//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new AnonymEntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

public function init()
    {
        $this->setName('emails')
             ->setLabel(/* @translate */ 'E-Mail Notifications');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailAccess',
        		'options' => array('label' => /* @translate */ 'receive E-Mail alert')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailAccessText',
        		'options' => array('label' => /* @translate */ 'Mailtext')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
            'name' => 'mailConfirmationText',
            'options' => array('label' => /* @translate */ 'Confirmation mail text')));
        
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailInvitationText',
        		'options' => array('label' => /* @translate */ 'Invitation mail text')));
        
        
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailRejectionText',
        		'options' => array('label' => /* @translate */ 'Rejection mail text')));
    }
    
    
}