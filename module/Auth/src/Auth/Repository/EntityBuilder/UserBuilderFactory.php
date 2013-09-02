<?php

namespace Auth\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\EntityBuilder as Builder;
use Core\Repository\Hydrator;
use Core\Entity\RelationCollection;

class UserBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $hydrator = new Hydrator\EntityHydrator();
        
        $builder = new Builder(
            $hydrator, 
            new \Auth\Entity\User(),
            new \Core\Entity\Collection()
        );
        
        
        
        return $builder;
    }

    
}