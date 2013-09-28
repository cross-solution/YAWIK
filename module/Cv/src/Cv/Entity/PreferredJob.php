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

class PreferredJob extends AbstractIdentifiableEntity
{
	/** 
	 * @var string
	 */
    protected $typeOfApplication;
    
    /** 
     * @var string
     */
    protected $preferredJob;
    
    /**
     * @var string
     **/
    protected $preferredLocation;
    
    /** willingness to travel, bool */
    protected $willingnessToTravel;
    
    /**
     * Apply for a job, internship or studies
     * 
     * @param string $typeOfApplication
     * @return \Cv\Entity\PreferredJob
     */
    public function setTypeOfApplication($typeOfApplication) 
    {
    	$this->typeOfApplication=$typeOfApplication;
    	return $this;
    } 
    
    /**
     * Gets the type of an Application
     * 
     * @return string
     */
    public function getTypeOfApplication()
    {
    	return $this->typeOfApplication;
    } 
    
    public function setPreferredJob($preferredJob)
    {
    	$this->preferredJob=$preferredJob;
    	return $this;
    }
    
    public function getPreferredJob()
    {
    	return $this->preferredJob;
    }
    
    public function setWillingnessToTravel($willingnessToTravel)
    {
    	$this->willingnessToTravel=$willingnessToTravel;
    	return $this;
    }
    
    public function getWillingnessToTravel()
    {
    	return $this->willingnessToTravel;
    }
}

    
 
    
   
   
}