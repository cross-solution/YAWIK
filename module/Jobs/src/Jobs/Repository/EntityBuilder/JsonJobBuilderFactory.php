<?php

namespace Jobs\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Entity\Hydrator;


class JsonJobBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('datePublishStart', new \Core\Repository\Hydrator\DatetimeStrategy(
            \Core\Repository\Hydrator\DatetimeStrategy::FORMAT_MONGO,
            \Core\Repository\Hydrator\DatetimeStrategy::FORMAT_ISO
        ));
        
        $builder = new JobBuilder(
            $hydrator, 
            new \Jobs\Entity\Job(),
            new \Core\Entity\Collection()
        );

        return $builder;
    }
    

    
}