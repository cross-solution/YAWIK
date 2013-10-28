<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Collection;

class AttachmentsFieldsetFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $services   = $serviceLocator->getServiceLocator();
        $repository = $services->get('repositories')->get('Applications/Files');
        $hydrator   = new \Core\Repository\Hydrator\FileUploadHydrator($repository);
        $fieldset   = new AttachmentsFieldset();
        
        $fieldset->setHydrator($hydrator);
        return $fieldset;
    }
    
    
}