<?php

namespace Jobs\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

interface JobInterface extends EntityInterface, IdentifiableEntityInterface
{

    public function setApplyId($applyId);
    public function getApplyId();
    
    public function getLink();
    public function setLink($link);
    
    public function getDatePublishStart();
    public function setDatePublishStart($datePublishStart);
    
    public function getTitle();
    public function setTitle($title);
    
    public function getCompany();
    public function setCompany($company);
    
    public function setContactEmail($email);
    public function getContactEmail();
    
    public function setUserId($userId);
    public function getUserId();
    public function setUser(UserInterface $user);
    public function getUser() ;
    
    public function setLocation($location);
    public function getLocation();
        
    public function setApplications(Collection $applications);
    public function getApplications();
    
}