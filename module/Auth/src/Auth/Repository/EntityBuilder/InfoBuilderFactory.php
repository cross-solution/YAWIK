<?php

namespace Auth\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Entity\RelationEntity;
use Core\Repository\EntityBuilder\RelationAwareBuilder;
use Core\Repository\Hydrator\EntityHydrator;
use Core\Repository\Hydrator\FileStrategy;
use Auth\Entity\Info;
use Core\Repository\Hydrator\EntityRelationStrategy;


class InfoBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $strategy = new EntityRelationStrategy($serviceLocator->getServiceLocator()->get('repositories')->get('Users/Files'));
        $hydrator = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        
        $builder = new RelationAwareBuilder($hydrator, new Info());
        $builder->setRelation(new RelationEntity(
                    array($serviceLocator->getServiceLocator()->get('mappers')->get('user'), 'findInfo')
                  ),  'id');
        
        return $builder;
    }
    
    
}