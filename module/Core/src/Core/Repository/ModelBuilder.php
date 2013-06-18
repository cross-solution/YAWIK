<?php

namespace Core\Repository;

class ModelBuilder
{
    
    public function __construct($hydrator, $modelPrototype, $collectionPrototype)
    {
        $this->hydrator = $hydrator;
        $this->modelPrototype = $modelPrototype;
        $this->collectionPrototype = $collectionPrototype;
    } 
    
    
    public function buildModel($data = null)
    {
        $model = clone $this->modelPrototype;
        if ($data) {
            $this->hydrator->hydrate($data, $model);
        }
        return $model;
    }
    
    public function unbuildModel($model)
    {
        $data = $this->hydrator->extract($model);
        return $data;
    }
    
    public function buildCollection($data = array())
    {
        if (!is_array($data) && !$data instanceOf Traversable) {
            die (__METHOD__ .': expects an array or instance of Traversable.');
        }
        
        $collection = clone $this->collectionPrototype;
        foreach ($data as $modelData) {
            $model = $this->buildModel($modelData);
            $collection->addModel($model);
        }
        return $collection;
    }
    
    public function unbuildCollection($collection)
    {
        $data = array();
        foreach ($collection as $model) {
            $data[] = $this->unbuildModel($model);
        }
        return $data;
    }
}