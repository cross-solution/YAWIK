<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Entity\RelationEntity;
use Auth\Entity\Info;
use Core\Repository\Hydrator\EntityRelationStrategy;
use Core\Repository\Hydrator\EntityHydrator;
use Core\Repository\EntityBuilder\RelationAwareBuilder;

class ContactBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {

        $strategy = new EntityRelationStrategy($serviceLocator->getServiceLocator()->get('repositories')->get('Applications/Files'));
        $hydrator = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);

        $builder = new RelationAwareBuilder($hydrator, new Info());
       
        $builder->setRelation(new RelationEntity(
            array($serviceLocator->getServiceLocator()->get('mappers')->get('application'), 'findContact')
        ), 'id');
        
        return $builder;
    }
    
        
}