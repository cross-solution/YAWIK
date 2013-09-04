<?php

namespace Core\Repository\EntityBuilder;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Core\Entity\EntityInterface;
use Core\Entity\CollectionInterface;
use Core\Entity\Collection;

class EntityBuilder implements EntityBuilderInterface
{
    
    protected $hydrator;
    protected $entityPrototype;
    protected $collectionPrototype;
    
    public function __construct($hydrator, $entityPrototype, $collectionPrototype=null)
    {
        $this->setHydrator($hydrator);
        $this->setEntityPrototype($entityPrototype);
        if (null !== $collectionPrototype) {
            $this->setCollectionPrototype($collectionPrototype);
        }
    }
    
    /**
     * @return the $hydrator
     */
    public function getHydrator ()
    {
        return $this->hydrator;
    }

	/**
     * @param field_type $hydrator
     */
    public function setHydrator (HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

	/**
     * @return the $entityPrototype
     */
    public function getEntity ()
    {
        return clone $this->entityPrototype;
    }

	/**
     * @param field_type $entityPrototype
     */
    public function setEntityPrototype (EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }

	/**
     * @return the $collectionPrototype
     */
    public function getCollection ()
    {
        if (!$this->collectionPrototype) {
            $this->setCollectionPrototype();
        }
        return clone $this->collectionPrototype;
    }

	/**
     * @param field_type $collectionPrototype
     */
    public function setCollectionPrototype (CollectionInterface $collection)
    {
        $this->collectionPrototype = $collection;
        return $this;
    }
    
    public function build($data = array())
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            die (__METHOD__.': Expects $data to be an array or implements Traversable.');
        }
        
        $entity = $this->getEntity();
        if (!empty($data)) {
            $hydrator = $this->getHydrator();
            $hydrator->hydrate($data, $entity);
        }
        return $entity;
    }
    
    public function unbuild(EntityInterface $entity)
    {
        $hydrator = $this->getHydrator();
        return $hydrator->extract($entity);
    }
    
    public function buildCollection($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            die (__METHOD__.': Expects $data to be an array or implements Traversable.');
        }
        
        $collection = $this->getCollection();
        foreach ($data as $row) {
            $entity = $this->build($row);
            $collection->add($entity);
        }
        return $collection;
    }
    
    public function unbuildCollection(CollectionInterface $collection)
    {
        $data = array();
        
        foreach ($collection as $entity) {
            $data[] = $this->unbuild($entity);
        }
        return $data;
    }

	
}