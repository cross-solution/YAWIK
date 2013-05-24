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
    
    public function removeModel($modelOrId)
    {
        $id = $modelOrId instanceOf \Core\Model\ModelInterface
            ? $modelOrId->getId()
            : $modelOrId;
        
        foreach ($this->collection as $key => $model) {
            if ($model->getId() == $id) {
                unset($this->collection[$key]);
                break;
            }
        }
        return $this;
    }
        
    public function removeModels()
    {
        $this->collection = array();
        return $this;
    } 
    
    public function addModels(array $models)
    {
        foreach ($models as $model) {
            $this->addModel($model);
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