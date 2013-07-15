<?php

namespace Applications\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Applications\Repository\Application;

class ApplicationRepositoryFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $applicationMapper = $serviceLocator->get('ApplicationMapper');
        $educationMapper   = $serviceLocator->get('EducationMapper');

        $repository = new Application($applicationMapper, $educationMapper);
        //$repository->setApplicationBuilder($serviceLocator->get('ApplicationBuilder'));
        return $repository;
    }

    
}