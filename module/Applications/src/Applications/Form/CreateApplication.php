<?php

namespace Applications\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\InjectAwareEntityHydrator as Hydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class CreateApplication extends Form implements ServiceLocatorAwareInterface
{
    protected $forms;
    protected $inputFilterSpecification;
    
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
            $hydrator = new Hydrator(array('attachments'));
            $hydrator->addStrategy('attachments', new \Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy());
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
            'required' => true
           
            
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
       
       
    }
    
}