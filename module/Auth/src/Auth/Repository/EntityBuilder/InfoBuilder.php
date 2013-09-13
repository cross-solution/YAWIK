<?php

namespace Auth\Repository\EntityBuilder;


use Core\Repository\EntityBuilder\RelationAwareBuilder;
use Core\Entity\EntityInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Core\Entity\RelationEntity;

class InfoBuilder extends RelationAwareBuilder 
{
    
    public function __construct()
    {
        
    }
    
    public function getEntity()
    {
        if (!$this->entityPrototype) {
            $this->setEntityPrototype(new \Auth\Entity\Info());
        }
        return clone $this->entityPrototype;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new \Core\Repository\Hydrator\EntityHydrator());
        }
        return $this->hydrator;
    }
    
    
}