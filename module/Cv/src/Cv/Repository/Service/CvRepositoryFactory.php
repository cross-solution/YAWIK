<?php

namespace Cv\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Cv\Repository\Cv;

class CvRepositoryFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $mapper = $serviceLocator->get('CvMapper');
        $builder = $serviceLocator->get('CvBuilder');
        
        $repository = new Cv($mapper);
        $repository->setCvBuilder($builder);
        //$repository->setApplicationBuilder($serviceLocator->get('ApplicationBuilder'));
        return $repository;
    }

    
}