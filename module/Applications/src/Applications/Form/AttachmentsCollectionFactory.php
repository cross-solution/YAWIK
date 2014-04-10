<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\FileCollection;
use Core\Entity\Hydrator\FileCollectionUploadHydrator;
use Applications\Entity\Attachment;

class AttachmentsCollectionFactory implements FactoryInterface
{
    
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $services   = $serviceLocator->getServiceLocator();
        $auth       = $services->get('AuthenticationService');
        $hydrator   = new FileCollectionUploadHydrator();
        $fileEntity = new Attachment();
        if ($auth->hasIdentity()) {
            $fileEntity->setUser($auth->getUser());
        }
        
        $collection = new FileCollection('attachments');
        $collection->setLabel('Attachments')
                   ->setHydrator($hydrator)
                   ->setCount(0)
                   ->setShouldCreateTemplate(true)
                   ->setAllowAdd(true)
                   ->setTargetElement($serviceLocator->get('file'))
                   ->setFileEntity($fileEntity);
        $config = $services->get('Config');
        if (isset($config['Applications']['allowedMimeTypes'])) {
            $validator = $services->get('validatorManager')->get(
                'filemimetype',
                $config['Applications']['allowedMimeTypes']
            );
            $collection->setFileValidator($validator);
        }

        return $collection;
    }

}