<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */


namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;

class Education extends AbstractIdentifiableEntity
{
	/** @var string */
    protected $startDate;
    
    /** @var string */
    protected $endDate;
    
    /** @var bool */
    protected $currentIndicator;
    
    /** @var string */
    protected $competencyName;
    
    /** @var string */
    protected $description;
    
    /** needed for europass */
    protected $nationalClassification;
    
    
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
    
    /**
     * marks the education as ongoing
     * 
     * @param bool $currentIndicator
     * @return \Cv\Entity\Education
     */
    public function setCurrentIndicator($currentIndicator)
    {
    	$this->currentIndicator=$currentIndicator;
    	return $this;
    }
    
    /**
     * indicates that the education is ongoing
     * 
     * @return bool
     */
    public function getCurrectIndicator() {
    	return $this->currentIndicator;
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