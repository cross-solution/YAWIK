<?php

namespace Auth\Repository\EntityBuilder;


use Core\Repository\EntityBuilder\RelationAwareBuilder;
use Core\Entity\EntityInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Core\Entity\RelationEntity;

class InfoBuilder extends RelationAwareBuilder implements MapperAwareInterface
{
    
    
    public function getRelation(EntityInterface $entity)
    {
        $relation = new RelationEntity(array(
            
        ))
    }
}