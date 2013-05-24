<?php

namespace Applications\Model;

use Core\Model\AbstractModel;

class Education extends AbstractModel
{
    protected $applicationId;
    protected $startDate;
    protected $endDate;
    protected $competencyName;
    protected $description;
    protected $nationalClassification;
    
    
    
    
    /**
     * @return the $applicationId
     */
    public function getApplicationId ()
    {
        return $this->applicationId;
    }

	/**
     * @param field_type $applicationId
     */
    public function setApplicationId ($applicationId)
    {
        $this->applicationId = $applicationId;
        return $this;
    }

	public function setStartDate($startDate)
    {
        $this->startDate = (string) $startDate;
        return $this;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setCompetencyName($competencyName)
    {
    	$this->competencyName = $competencyName;
    	return $this;
    }
    
    public function getCompetencyName()
    {
    	return $this->competencyName;
    } 
    
    public function setDescription($value)
    {
    	$this->description = $value;
    	return $this;
    }
    
    public function getDescription()
    {
    	return $this->description;
    }
}