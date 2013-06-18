<?php

namespace Core\Model;

class RelationCollection implements CollectionInterface, RelationCollectionInterface
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
    
    public function addModel(ModelInterface $model)
    {
        $this->loadCollection();
        $this->collection->addModel($model);
        return $this;
    }
    
    public function removeModel($modelOrId)
    {
        $this->loadCollection();
        $this->collection->removeModel($modelOrId);
        return $this;
    }
        
    public function removeModels()
    {
        $this->loadCollection();
        $this->collection->removeModels();
        return $this;
    } 
    
    public function addModels(array $models)
    {
        $this->loadCollection();
        $this->collection->addModels($models);
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
    
    public function isCollectionLoaded()
    {
        return null !== $this->collection;
    }
    
    protected function loadCollection()
    {
        if ($this->isCollectionLoaded()) {
            return;
        }
        
        $collection = call_user_func_array($this->callback, $this->params);
        if (!$collection instanceOf CollectionInterface) {
            die (__METHOD__ . ': Callback must return CollectionInterface.');
        }
        $this->collection = $collection;
        
    }
}