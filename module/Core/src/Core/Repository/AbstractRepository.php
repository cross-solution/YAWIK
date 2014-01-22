<?php

namespace Core\Repository;


use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB\DocumentRepository;

abstract class AbstractRepository implements RepositoryInterface
{
    protected $documentRepository;
    protected $entityPrototype;

    public function setDocumentRepository(DocumentRepository $repository)
    {
        $this->documentRepository = $repository;
        return $this;
    }
    
    public function getDocumentRepository()
    {
        return $this->documentRepository;
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
    
    /**
     * Proxy all method calls to $this->documentRepository
     * 
     * @see \Doctrine\ODM\MongoDB\DocumentRepository::__call()
     */
    public function __call($method, $params)
    {
        $return = call_user_func_array(array($this->getDocumentRepository(), $method), $params);
        return null == $return ? $this : $return;
    }

}