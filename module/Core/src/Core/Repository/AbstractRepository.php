<?php

namespace Core\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\EntityInterface;

abstract class AbstractRepository implements RepositoryInterface, Mapper\MapperAwareInterface
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
    
    protected function getMapper($name)
    {
        return $this->getMapperManager()->get($name);
    }
    public function find($id) {}
    public function fetchAll() {}
    public function create($data = null) {}
    public function save(EntityInterface $entity) {}
    
}