<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;

interface EducationInterface extends EntityInterface
{
    public function setStartDate($date);
    public function getStartDate();
    
    public function setEndDate($date);
    public function getEndDate();
    
    public function setCurrentIndicator($current);
    public function getCurrentIndicator();
    
    /*
     * name of the qualification. 
     */
    public function setCompetencyName($name);
    public function getCompetencyName();
    
    public function setDescription($description);
    public function getDescription();
    
    public function setOrganizationName($name);
    public function getOrganizationName();
    
    public function setOrganizationCity($city);
    public function getOrganizationCity();
    
    public function setOrganizationCountry($country);
    public function getOrganizationCountry();
    
    /*
     * http://ec.europa.eu/eqf/home_de.htm
     */
    public function setNationalClassification($eqr);
    public function getNationalClassification();
    
    
}