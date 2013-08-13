<?php

namespace Cv\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;

class Cv extends AbstractRepository
{
    
    

	

	public function find($id, $mode = false)
    {
        $entity = $mode
                ? $this->getMapper('cv')->find($id)
                : $this->getMapper('cv')->find(
                      $id, 
                      array('educations', 'employments'),
                      /*exclude*/ true
                  );
        return $entity;
    }
    
    public function save(EntityInterface $entity)
    {
        $this->getMapper('cv')->save($entity);
    }
    
    
}