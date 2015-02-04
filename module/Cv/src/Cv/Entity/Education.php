<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */


namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Education extends AbstractIdentifiableEntity
{
	/** @var string 
	 * @ODM\String
	 */
    protected $startDate;
    
    /** @var string 
     * @ODM\String */
    protected $endDate;
    
    /** @var bool 
     * @ODM\Boolean*/
    protected $currentIndicator;
    
    /** @var string */
    protected $competencyName;
    
    /** @ODM\String
     * 
     */
    protected $organizationName;
    
    /** @var string
     * @ODM\String */
    protected $description;
    
    /** needed for europass 
     * @ODM\String*/
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
    
    /**
     * @return the $organizationName
     */
    public function getOrganizationName ()
    {
        return $this->organizationName;
    }

	/**
     * @param field_type $organizationName
     */
    public function setOrganizationName ($organizationName)
    {
        $this->organizationName = $organizationName;
        return $this;
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