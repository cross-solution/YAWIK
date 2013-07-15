<?php

namespace Core\Entity;

use Core\Entity\EntityInterface;

interface RelationCollectionInterface 
{
    public function isCollectionLoaded();
    public function setCallback($callable, array $params = array());
    public function setParams(array $params);
    
}
