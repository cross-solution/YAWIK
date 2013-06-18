<?php

namespace Core\Model;

use Core\Model\ModelInterface;

interface RelationCollectionInterface 
{
    public function isCollectionLoaded();
    public function setCallback($callable, array $params = array());
    public function setParams(array $params);
    
}
