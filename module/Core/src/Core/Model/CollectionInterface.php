<?php

namespace Core\Model;

use Core\Model\ModelInterface;

interface CollectionInterface extends \IteratorAggregate, \Countable
{
    public function addModel(ModelInterface $model);
    public function addModels(array $models); 
    
    public function removeModel($modelOrId);
    public function removeModels();
}
