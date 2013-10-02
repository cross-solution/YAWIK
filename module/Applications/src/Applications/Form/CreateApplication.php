<?php

namespace Applications\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class CreateApplication extends Form implements ServiceLocatorAwareInterface, InputFilterProviderInterface
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
        $this->setName('create-application-form');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'jobId',
            'required' => true,
            'options' => array(
                'required' => true
            )
            
        ));
        
        $this->add($this->forms->get('user-info-fieldset')
                               ->setLabel('personal informations')
                               ->setName('contact')
                               ->setObject($this->forms->getServiceLocator()->get('builders')->get('auth-info')->getEntity()));
        $this->add(
            $this->forms->get('CvFieldset')
                        ->setObject($this->forms->getServiceLocator()->get('builders')->get('Cv')->getEntity())
        );
        $this->add(
            $this->forms->get('Applications/AttachmentsCollection')
        );
        $this->add($this->forms->get('DefaultButtonsFieldset'));
        $this->get('cv')->get('educations')->setCount(1)->prepareFieldset();
       
    }
    
    public function getInputFilterSpecification()
    {
        
        return array(
            'jobId' => array(
                'required' => true
            )
        );
    }
}