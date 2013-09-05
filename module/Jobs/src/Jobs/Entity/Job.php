<?php

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Repository\Mapper\MapperAwareInterface;
use Core\Entity\CollectionInterface;


class Job extends AbstractIdentifiableEntity implements JobInterface
{

    protected $applyId;
    protected $userId;
    protected $user;
    protected $applications;
    
    public function setApplyId($applyId)
    {
        $this->applyId = (string) $applyId;
        return $this;
    }
    
    public function getApplyId()
    {
        return $this->applyId;
    }
    
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }
    
    public function getUserId()
    {
        return $this->userId;
    }
    
    public function injectUser(EntityInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function injectApplications(CollectionInterface $applications)
    {
        $this->applications = $applications;
        return $this;
    }
    
    public function getApplications()
    {
        return $this->applications;
    }
}