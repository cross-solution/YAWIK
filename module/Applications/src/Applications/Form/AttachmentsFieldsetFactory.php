<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Element\Collection;

class AttachmentsFieldsetFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
  //      $services   = $serviceLocator->getServiceLocator();
 //       $repository = $services->get('repositories')->get('Applications/Files');
//        $hydrator   = new \Core\Repository\Hydrator\FileUploadHydrator($repository);
        $hydrator     = new EntityHydrator();
        $fieldset   = new AttachmentsFieldset();
        
        $fieldset->setHydrator($hydrator);
        return $fieldset;
    }
    
    
}