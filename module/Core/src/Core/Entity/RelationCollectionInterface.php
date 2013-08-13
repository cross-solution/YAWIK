<?php

namespace Core\Entity;



interface RelationCollectionInterface 
{
    public function isCollectionLoaded();
    public function setCallback($callable, array $params = array());
    public function setParams(array $params);
    
}
