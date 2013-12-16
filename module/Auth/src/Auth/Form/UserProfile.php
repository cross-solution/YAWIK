<?php

namespace Auth\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
//use Zend\InputFilter\InputFilterProviderInterface;

class UserProfile extends Form implements ServiceLocatorAwareInterface//, InputFilterProviderInterface
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
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
	public function init()
    {
        $this->setName('user-profile-form');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
        ));
        
        
        $this->add(
            $this->forms->get('user-info-fieldset')
                        //->setUseAsBaseFieldset(true)
        );
        $this->add(
            $this->forms->get('Auth/UserBaseFieldset')
        );
        
        $this->add($this->forms->get('DefaultButtonsFieldset'));
       
    }
    
    public function setObject($object)
    {
        $this->get('base')->setObject($object);
        return parent::setObject($object);
        
    }
    
    
}