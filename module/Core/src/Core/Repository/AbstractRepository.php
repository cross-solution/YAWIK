<?php

namespace Core\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\EntityInterface;

abstract class AbstractRepository implements RepositoryInterface, Mapper\MapperAwareInterface
{
    const LOAD_EAGER = 'EAGER';
    const LOAD_LAZY  = 'LAZY';
    
    const MODE_DEFAULT = 0;
    const MODE_FORCE_ENTITY = 1;
    
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