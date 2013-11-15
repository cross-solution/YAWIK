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
        $this->setName('settings-core-fieldset')
             ->setLabel(/* @translate */ 'E-Mail Notifications');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailAccess',
        		'options' => array('label' => /* @translate */ 'receive E-Mail alert')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailAccessText',
        		'options' => array('label' => /* @translate */ 'Mailtext')));
        
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailInvitation',
        		'options' => array('label' => /* @translate */ 'send Invitation')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailInvitationText',
        		'options' => array('label' => /* @translate */ 'Mailtext')));
        
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailAcknowledgement',
        		'options' => array('label' => /* @translate */ 'acknowledgement of receipt')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailAcknowledgementText',
        		'options' => array('label' => /* @translate */ 'Mailtext')));
        
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailDecline',
        		'options' => array('label' => /* @translate */ 'decline E-Mail')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailDeclineText',
        		'options' => array('label' => /* @translate */ 'Mailtext')));
    }
    
    
}