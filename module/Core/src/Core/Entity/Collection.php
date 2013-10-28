<?php

namespace Core\Entity;

class Collection implements CollectionInterface
{
    protected $collection = array();
    
    public function __construct(array $entities=array())
    {
        $this->addEntities($entities);
    }
    
    
    public function add(EntityInterface $entity)
    {
        $this->collection[] = $entity;
        return $this;
    }
    
    public function remove($entityOrId)
    {
        $id = $entityOrId instanceOf \Core\Entity\EntityInterface
            ? $entityOrId->getId()
            : $entityOrId;
        
        $this->collection = array_filter(
            $this->collection,
            function ($entity) use ($id) { return $entity->getId() != $id; }
        );
        return $this;
    }
        
    public function removeEntities()
    {
        $this->collection = array();
        return $this;
    } 
    
    public function addEntities(array $entities)
    {
        foreach ($entities as $entity) {
            $this->add($entity);
        }
        return $this;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }

    public function count()
    {
        return count($this->collection);
    }
}