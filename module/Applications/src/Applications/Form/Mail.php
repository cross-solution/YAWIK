<?php

namespace Applications\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\InjectAwareEntityHydrator as Hydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class Mail extends Form
{
    
    
	public function init()
    {
        $this->setName('applicant-mail');


        
        $this
        ->add(array(
            'type' => 'hidden',
            'name' => 'applicationId',
        ))
        ->add(array(
            'type' => 'hidden',
            'name' => 'status',
        ))
        ->add(array(
            'name' => 'mailSubject',
        ))
        ->add(array(
            'type' => 'textarea',
            'name' => 'mailText'
        ));
            
           
    }
    
    
}