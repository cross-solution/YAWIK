<?php

namespace Jobs\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\Hydrator;


class JobBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('datePublishStart', new Hydrator\DatetimeStrategy());
        
        $builder = new JobBuilder(
            $hydrator, 
            new \Jobs\Entity\Job(),
            new \Core\Entity\Collection()
        );

        return $builder;
    }
    

    
}