<?php

namespace Core\Entity;



interface RelationInterface 
{
    public function isLoaded();
    public function setCallback($callable, array $params = array());
    public function setParams(array $params);
    
}
