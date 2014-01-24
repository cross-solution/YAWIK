<?php

namespace Auth\Repository;

use \Auth\Entity\Info;
use Core\Entity\EntityInterface;
use Core\Repository\AbstractRepository;



class User extends AbstractRepository
{
    
    public function findByProfileIdentifier($identifier)
    {
        $entity = $this->findOneBy(array('profile.identifier' => $identifier));
        return $entity;
    }
    
    public function findByLogin($login) {
        $entity = $this->findOneBy(array('login' => $login));
        return $entity;
    }
    
    /**
     * 
     * @param \Auth\Entity\Info $info
     */
    public function copyUserInfo(Info $info){
        $contact = new Info();
        $contact->fromArray(Info::toArray($info));
    }
    
     
}