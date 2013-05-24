<?php

namespace Applications\Repository\MongoDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Applications\Repository\MongoDb\Application;

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