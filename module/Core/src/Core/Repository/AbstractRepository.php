<?php

namespace Core\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\EntityInterface;

abstract class AbstractRepository implements RepositoryInterface, Mapper\MapperAwareInterface
{
    const LOAD_EAGER = 'EAGER';
    const LOAD_LAZY  = 'LAZY';
    
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
    public function find($id, $mode=self::LOAD_LAZY) {}
    public function fetch() {}
    public function create($data = null) {}
    public function save(EntityInterface $entity) {}
    
   
    
}