<?php

namespace Auth\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Auth\Repository\User;

class UserRepositoryFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $userMapper = $serviceLocator->get('UserMapper');

        $repository = new User($userMapper);
        //$repository->setApplicationBuilder($serviceLocator->get('ApplicationBuilder'));
        return $repository;
    }

    
}