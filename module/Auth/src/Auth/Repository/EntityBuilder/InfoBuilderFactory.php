<?php

namespace Auth\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\Hydrator;
use Core\Entity\RelationEntity;


class InfoBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $builder = new InfoBuilder();
        $builder->setRelation(new RelationEntity(
                    array($serviceLocator->getServiceLocator()->get('mappers')->get('user'), 'findInfo')
                  ),  'id');
            
        return $builder;
    }
    
    
}