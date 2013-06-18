<?php

namespace Applications\Model;

use Core\Model\ModelInterface;
use Core\Model\DateFormatEnabledInterface;

interface ApplicationInterface extends ModelInterface, DateFormatEnabledInterface 
{
   
    public function setJobId($jobId);
    public function getJobId();
    
    public function setDateCreated($dateCreated);
    public function getDateCreated($format=null);
    
    public function setDateModified($dateModified);
    public function getDateModified($format=null);
    
    public function setTitle($title);
    public function getTitle();
    
    public function setFirstname($firstname);
    public function getFirstname();
    
    public function setLastname($lastname);
    public function getLastname();
    
    public function setStreet($street);
    public function getStreet();
    
    public function setHouseNumber($houseNumber);
    public function getHouseNumber();
    
    public function setZipCode($zipCode);
    public function getZipCode();
    
    public function setLocation($location);
    public function getLocation();
    
    public function setPhoneNumber($phoneNumber);
    public function getPhoneNumber();
    
    public function setMobileNumber($mobileNumber);
    public function getMobileNumber();
    
    public function setEmail($email);
    public function getEmail();
    
    
}
