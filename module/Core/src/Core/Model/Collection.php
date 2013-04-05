<?php

namespace Core\Model;

class Collection implements CollectionInterface
{
    protected $collection = array();
    
    public function __construct(array $models=array())
    {
        $this->addModels($models);
    }
    
    
    public function addModel(ModelInterface $model)
    {
        $this->collection[] = $model;
        return $this;
    }
    
    public function addModels(array $models)
    {
        foreach ($models as $model) {
            $this->addModel($model);
        }
        return $this;
    }
    
    public function rewind()
    {
        reset($this->collection);
        return $this;
    }
    
    public function current()
    {
        return current($this->collection);
    }
    
    public function key()
    {
        return key($this->collection);
    }
    
    public function next()
    {
        return next($this->collection);
    }
    
    public function valid()
    {
        return null !== $this->key(); 
    }
    
    public function count()
    {
        return count($this->collection);
    }
}