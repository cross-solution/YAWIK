<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\CollectionInterface;
use Core\Entity\IdentifiableEntityInterface;

interface ApplicationInterface extends EntityInterface, IdentifiableEntityInterface 
{
    
    public function setJobId($jobId);
    public function getJobId();
    
    public function getJob();
    
    public function setUserId($userId);
    public function getUserId();
    
    public function injectUser(EntityInterface $user);
    public function getUser();
    
    public function setStatus($status);
    public function getStatus();
    
    public function setDateCreated($dateCreated);
    public function getDateCreated($format=null);
    
    public function setDateModified($dateModified);
    public function getDateModified($format=null);
    
    public function setContact(EntityInterface $contact);
    public function getContact();
    
    public function setSummary($summary);
    public function getSummary();
    
    public function setCv(EntityInterface $cv);
    public function getCv();
    
    public function setHistory(HistoryCollectionInterface $history);
    public function getHistory(); 
    
    public function setReadBy(array $userIds);
    public function getReadBy();
    public function addReadBy($userOrId);
    public function isUnreadBy($userOrId);
    public function isReadBy($userOrId);
    
}
