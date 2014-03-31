<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\SearchableEntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\PermissionsAwareInterface;

interface ApplicationInterface 
    extends EntityInterface, 
            IdentifiableEntityInterface, 
            SearchableEntityInterface,
            PermissionsAwareInterface
{
    
    public function setJobId($jobId);
    public function getJobId();
    
    public function getJob();
    
    public function setUser(UserInterface $user);
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
    
    public function setHistory(Collection $history);
    public function getHistory(); 
    
    public function setReadBy(array $userIds);
    public function getReadBy();
    public function addReadBy($userOrId);
    public function isUnreadBy($userOrId);
    public function isReadBy($userOrId);
    
    /**
     * Gets all comments for the application.
     * 
     * @return Collection
     */
    public function getComments();
    
    /**
     * Sets comment collection for the application.
     * 
     * @param Collection $comments
     * @return ApplicationInterface
     */
    public function setComments(Collection $comments);
    
}
