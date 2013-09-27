<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\FileBuilder as Builder;
use Core\Repository\Hydrator;


class FileBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('dateUploaded', new \Core\Repository\Hydrator\DatetimeStrategy());
        
        $builder = new Builder(
            $hydrator, 
            new \Core\Entity\FileEntity(),
            new \Core\Entity\Collection()
        );
        
        return $builder;
    }

    
}