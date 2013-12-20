<?php

namespace Auth\Repository;

use Zend\ServiceManager\FactoryInterface;
use Auth\Repository\FileRepository;

class FileRepositoryFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $mapper     = $serviceLocator->getServiceLocator()->get('mappers')->get('Users/Files');
        $repository = new FileRepository($mapper);

        return $repository;
    }

    
}