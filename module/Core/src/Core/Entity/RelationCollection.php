<?php

namespace Core\Entity;

class RelationCollection implements CollectionInterface, RelationInterface
{
    protected $collection = null;
    protected $callback;
    protected $params;
    
    public function __construct($callable, array $params = array())
    {
        $this->setCallback($callable, $params);
    }
    
    public function setCallback($callable, array $params = array())
    {
        if (!is_callable($callable)) {
            die (__METHOD__ . ': Callback must be callable.');
        }
        $this->callback = $callable;
        $this->setParams($params);
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }
    
    public function add(EntityInterface $entity)
    {
        $this->loadCollection();
        $this->collection->add($entity);
        return $this;
    }
    
    public function remove($entityOrId)
    {
        $this->loadCollection();
        $this->collection->remove($entityOrId);
        return $this;
    }
    
    public function removeEntities()
    {
        $this->loadCollection();
        $this->collection->removeEntities();
        return $this;
    }
        
    public function addEntities(array $entities)
    {
        $this->loadCollection();
        $this->collection->addModels($entities);
        return $this;
    }
    
    public function getIterator()
    {
        $this->loadCollection();
        return $this->collection->getIterator();
    }

    public function count()
    {
        $this->loadCollection();
        return $this->collection->count();
    }
    
    public function isLoaded()
    {
        return null !== $this->collection;
    }
    
    protected function loadCollection()
    {
        if ($this->isLoaded()) {
            return;
        }
        
        $collection = call_user_func_array($this->callback, $this->params);
        if (!$collection instanceOf CollectionInterface) {
            die (__METHOD__ . ': Callback must return CollectionInterface.');
        }
        $this->collection = $collection;
        
    }
}