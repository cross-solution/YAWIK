<?php

namespace Core\Repository;


use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB\DocumentRepository;

abstract class AbstractRepository extends DocumentRepository implements RepositoryInterface
{

    protected $entityPrototype;

    public function getService($name)
    {
        return $this->dm->getConfiguration()->getServiceLocator()->get($name);
    }
    
    public function setEntityPrototype(EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }

    public function create(array $data=null) {
        if (null === $this->entityPrototype) {
            throw new \RuntimeException('Could not create an entity. No protoype is set!');
        }

        $entity = clone $this->entityPrototype;
        
        if (null !== $data) {
            foreach ($data as $property => $value) {
                $entity->$property = $value;
            }
        }
        
        return $entity;
    }

}