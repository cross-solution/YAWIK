<?php

namespace Core\Repository;


use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB\DocumentRepository;

abstract class AbstractRepository extends DocumentRepository implements RepositoryInterface
{

    protected $entityPrototype;

    public function setEntityPrototype(EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }

    public function create() {
        if (null === $this->entityPrototype) {
            throw new \RuntimeException('Could not create an entity. No protoype is set!');
        }

        return clone $this->entityPrototype;
    }

}