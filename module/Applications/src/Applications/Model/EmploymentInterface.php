<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface EmploymentInterface extends ModelInterface
{
    public function setStartDate($date);
    public function getStartDate();
    
    public function setEndDate($date);
    public function getEndDate();
    
    public function setCurrentIndicator($current);
    public function getCurrentIndicator();
        
    public function setDescription($description);
    public function getDescription();
    
    public function setOrganizationName($name);
    public function getOrganizationName();
    
    public function setOrganizationCity($city);
    public function getOrganizationCity();
    
    public function setOrganizationCountry($country);
    public function getOrganizationCountry();
    

    
    
}