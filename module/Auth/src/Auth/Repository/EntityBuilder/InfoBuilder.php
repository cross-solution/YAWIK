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
    
    public function build($data = array())
    {
        $entity = parent::build($data);
        if (!empty($data) && isset($data['imageId'])) {
            $entity->injectImage(new RelationEntity(
                array($this->mappers->get('Applications/Files'), 'find'),
                array($data['imageId'])
            ));
        }
        return $entity;
    }
    
}