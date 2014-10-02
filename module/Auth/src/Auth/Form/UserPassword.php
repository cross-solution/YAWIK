<?php

namespace Auth\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Form\Hydrator\UserPasswordHydrator;
//use Zend\InputFilter\InputFilterProviderInterface;

class UserPassword extends Form implements ServiceLocatorAwareInterface//, InputFilterProviderInterface
{
    protected $forms;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->forms = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->forms;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new UserPasswordHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('user-password-form');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
        ));
        
        $this->add(
            $this->forms->get('Auth/UserPasswordFieldset')
                        //->setUseAsBaseFieldset(true)
        );
        
        $this->add($this->forms->get('submitField'));
    }
    
}