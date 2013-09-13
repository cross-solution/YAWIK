<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\AggregateBuilder as Builder;
use Core\Repository\Hydrator;
use Core\Repository\EntityBuilder\AbstractCopyableBuilderFactory;
use Auth\Repository\EntityBuilder\InfoBuilder;
use Core\Entity\RelationEntity;

class ContactBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $builder = new InfoBuilder();
        $builder->setRelation(new RelationEntity(
            array($serviceLocator->getServiceLocator()->get('mappers')->get('application'), 'findContact')
        ), 'id');
        return $builder;        
    }
    
        
}