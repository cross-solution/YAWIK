<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;

interface RepositoryInterface 
{
    
    public function find($id);
    public function fetch();
    public function create($data = null);
    public function save(EntityInterface $entity);
    
}