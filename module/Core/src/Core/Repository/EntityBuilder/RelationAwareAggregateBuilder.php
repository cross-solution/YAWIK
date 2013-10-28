<?php

namespace Core\Repository\EntityBuilder;


use Core\Entity\EntityInterface;
use Core\Entity\RelationInterface;

class RelationAwareAggregateBuilder extends AggregateBuilder implements RelationAwareInterface
{
    
    protected $relationCollection;
    protected $propertiesAsParamsMap = array();
  
    
    public function setRelation(RelationInterface $collection, $propertiesAsParamsMap = array())
    {
        $this->relationCollection = $collection;
        $this->propertiesAsParamsMap = (array) $propertiesAsParamsMap;
        return $this;
    }
    
    public function getRelation(EntityInterface $entity)
    {
        $relation = clone $this->relationCollection;
        
        $params = array_map(
            function ($property) use ($entity) {
                return $entity->{"get$property"}();
            },
            $this->propertiesAsParamsMap
        );
        
        $relation->setParams($params);
        return $relation;
    }
    
    
}