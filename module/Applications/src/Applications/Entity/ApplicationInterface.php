<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;

interface ApplicationInterface extends EntityInterface, IdentifiableEntityInterface 
{
   
    public function setJobId($jobId);
    public function getJobId();
    
    public function setStatus($status);
    public function getStatus();
    
    public function setDateCreated($dateCreated);
    public function getDateCreated($format=null);
    
    public function setDateModified($dateModified);
    public function getDateModified($format=null);
    
    public function setCv(EntityInterface $cv);
    public function getCv();
    
}
