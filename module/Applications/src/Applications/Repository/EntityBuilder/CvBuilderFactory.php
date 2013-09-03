<?php

namespace Applications\Repository\EntityBuilder;

use Cv\Repository\EntityBuilder\CvBuilderFactory as BaseCvBuilderFactory;
use Core\Entity\RelationEntity;

class CvBuilderFactory extends BaseCvBuilderFactory
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        $builder  = parent::createService($serviceLocator);
        $callback = array($serviceLocator->getServiceLocator()->get('mappers')->get('application'), 'fetchCv');
        $relation = new RelationEntity($callback); 
        $builder->setRelation($relation, 'id');
        return $builder;
        
    }
    
    protected function getBuilderName($builderName)
    {
        return "application-cv-$builderName";
    }
    
    protected function getBuilderClass()
    {
        return '\\Core\\Repository\\EntityBuilder\\RelationAwareAggregateBuilder';
    }
        
}