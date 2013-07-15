<?php

namespace Auth\Repository;

use Core\Entity\EntityInterface;



class User 
{
    
    protected $userMapper;
    
    protected $userBuilder;
    
    
    public function __construct($userMapper)
    {
        $this->userMapper = $userMapper;
    }
    
    public function getUserBuilder()
    {
        if (!$this->userBuilder) {
            $builder = new \Core\Repository\EntityBuilder(
                new \Core\Entity\Hydrator\EntityHydrator(),
                new \Auth\Entity\User(),
                new \Core\Entity\Collection()
            );
            $this->setUserBuilder($builder);
        }
        return $this->userBuilder;
    }
    
    public function setUserBuilder($entityBuilder)
    {
        $this->userBuilder = $entityBuilder;
        return $this;
    }
    
    public function findByProfileIdentifier($identifier)
    {
        $userData = $this->userMapper->findByProfileIdentifier($identifier);
        
        $entity = $this->getUserBuilder()->build($userData);
        return $entity;
    }
    
	public function find ($id)
    {
        $userData = $this->userMapper->find($id);
        
        
        $entity = $this->getUserBuilder()->build($userData);
        return $entity;
    }
    
    public function save($user)
    {
        $data = $this->getUserBuilder()->unbuild($user);
        
        $id = $this->userMapper->save($data);
        $user->setId($id);
    }
    
     
}