<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\FileCollection;
use Applications\Repository\Hydrator\FileUploadHydrator;
use Core\Entity\Hydrator\EntityHydrator;

class AttachmentsCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $services   = $serviceLocator->getServiceLocator();
        $repository = $services->get('repositories')->get('Applications/Files');
        $hydrator   = new FileUploadHydrator($repository);
        $hydrator->setAuth($services->get('AuthenticationService'));
                   
        $collection = new FileCollection('attachments');
        $collection->setLabel('Attachments')
                   ->setHydrator($hydrator)
                   ->setCount(0)
                   ->setShouldCreateTemplate(true)
                   ->setAllowAdd(true)
                   ->setTargetElement($serviceLocator->get('file'));
                   
        return $collection;
    }
    
    
}