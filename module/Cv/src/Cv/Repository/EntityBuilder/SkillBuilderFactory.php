<?php

namespace Cv\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\RelationAwareBuilder as Builder;
use Core\Repository\Hydrator;
use Core\Entity\RelationCollection;

class SkillBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $hydrator = new Hydrator\EntityHydrator();
        
        $builder = new Builder(
            $hydrator, 
            new \Cv\Entity\Skill(),
            new \Core\Entity\Collection()
        );
        
        $mapper = $serviceLocator->getServiceLocator()->get('mappers')->get('cv');
        $relation = new RelationCollection(array($mapper, 'fetchSkills'));
        
        $builder->setRelation($relation, /*propToParamMap*/ 'id');
        
        return $builder;
    }

    
}