<?php

namespace Core\Entity;

use Core\Entity\EntityInterface;

interface CollectionInterface extends \IteratorAggregate, \Countable
{
    public function add(EntityInterface $entity);
    public function addEntities(array $entity); 
    
    public function remove($entityOrId);
    public function removeEntities();
}
