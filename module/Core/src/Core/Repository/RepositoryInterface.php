<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;

interface RepositoryInterface 
{
    public function setEntityPrototype(EntityInterface $entity);
    public function create(array $data=null);
}