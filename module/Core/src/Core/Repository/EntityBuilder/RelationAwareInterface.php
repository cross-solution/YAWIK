<?php

namespace Core\Repository\EntityBuilder;


use Core\Entity\RelationInterface;
use Core\Entity\EntityInterface;

interface RelationAwareInterface
{
    
    public function setRelation(RelationInterface $collection);
    public function getRelation(EntityInterface $parent);

}