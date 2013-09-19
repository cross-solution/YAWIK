<?php

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Core\Entity\CollectionInterface;

class Job extends AbstractIdentifiableEntity implements JobInterface {

    protected $applyId;
    protected $title;
    protected $company;
    protected $userId;
    protected $user;
    protected $applications;
    protected $location;
    protected $link;
    protected $datePublishStart;
    
    public function setApplyId($applyId) {
        $this->applyId = (string) $applyId;
        return $this;
    }

    public function getApplyId() {
        return $this->applyId;
    }

    /**
     * @return the $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param field_type $title
     */
    public function setTitle($title) {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * @return the $company
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * @param field_type $company
     */
    public function setCompany($company) {
        $this->company = $company;
        return $this;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    public function getUserId() {
        return $this->userId;
    }
    
    public function setLocation($location)
    {
    	$this->location = $location;
    	return $this;
    }
    
    public function getLocation()
    {
    	return $this->location;
    }
    
    public function injectUser(EntityInterface $user) {
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function injectApplications(CollectionInterface $applications) {
        $this->applications = $applications;
        return $this;
    }

    public function getApplications() {
        return $this->applications;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($link) {
        $this->link = $link;
        return $this;
    }
    
    public function getDatePublishStart() {
        return $this->datePublishStart;
    }

    public function setDatePublishStart($datePublishStart) {
        $this->datePublishStart = $datePublishStart;
        return $this;
    }
    
}