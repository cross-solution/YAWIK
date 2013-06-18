<?php

namespace Applications\Repository\MongoDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Applications\Repository\MongoDb\Application;

class ApplicationBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $application = new \Applications\Model\Application();
        $hydrator = new \Core\Model\Hydrator\ModelHydrator();
        $collection = new \Core\Model\Collection();
        
        $builder = new \Core\Repository\ModelBuilder($hydrator, $application, $collection);
        return $builder;
        
    }

    
}