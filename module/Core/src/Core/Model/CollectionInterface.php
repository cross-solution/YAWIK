<?php

namespace Core\Model;

use Core\Model\ModelInterface;

interface CollectionInterface extends \Iterator, \Countable
{
    public function addModel(ModelInterface $model);
    public function addModels(array $models); 
    
}
