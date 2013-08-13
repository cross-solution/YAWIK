<?php

namespace Core\Repository\EntityBuilder;


use Core\Entity\RelationCollectionInterface;
use Core\Entity\EntityInterface;

interface RelationAwareInterface
{
    
    public function setRelation(RelationCollectionInterface $collection);
    public function getRelation(EntityInterface $parent);

}