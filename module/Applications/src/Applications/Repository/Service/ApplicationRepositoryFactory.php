<?php

namespace Applications\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Applications\Repository\Application;

class ApplicationRepositoryFactory implements FactoryInterface
{
	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @todo what happens here?
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $repository = new Application();
        return $repository;
    }

    
}