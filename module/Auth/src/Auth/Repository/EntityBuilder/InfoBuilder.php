<?php

namespace Auth\Repository\EntityBuilder;


use Core\Repository\EntityBuilder\RelationAwareBuilder;
use Core\Entity\EntityInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Core\Entity\RelationEntity;
use Zend\ServiceManager\ServiceLocatorInterface;

class InfoBuilder extends RelationAwareBuilder implements MapperAwareInterface
{
    
    protected $mappers;
    
    
    public function setMapperManager(ServiceLocatorInterface $mapperManager)
    {
        $this->mappers = $mapperManager;
        return $this;
    }
    
    public function getMapperManager()
    {
        return $this->mappers;
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