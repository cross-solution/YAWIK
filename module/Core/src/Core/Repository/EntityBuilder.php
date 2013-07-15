<?php

namespace Core\Repository;

class EntityBuilder
{
    protected $hydrator;
    protected $entityPrototype;
    protected $collectionPrototype;
    
    
    public function __construct($hydrator, $entityPrototype, $collectionPrototype)
    {
        $this->hydrator = $hydrator;
        $this->entityPrototype = $entityPrototype;
        $this->collectionPrototype = $collectionPrototype;
    } 
    
    
    public function build($data = null)
    {
        $entity = clone $this->entityPrototype;
        if ($data) {
            $this->hydrator->hydrate($data, $entity);
        }
        return $entity;
    }
    
    public function unbuild($entity)
    {
        $data = $this->hydrator->extract($entity);
        return $data;
    }
    
    public function buildCollection($data = array())
    {
        if (!is_array($data) && !$data instanceOf Traversable) {
            die (__METHOD__ .': expects an array or instance of Traversable.');
        }
        
        $collection = clone $this->collectionPrototype;
        foreach ($data as $entityData) {
            $entity = $this->build($entityData);
            $collection->add($entity);
        }
        return $collection;
    }
    
    public function unbuildCollection($collection)
    {
        $data = array();
        foreach ($collection as $entity) {
            $data[] = $this->unbuild($entity);
        }
        return $data;
    }
}