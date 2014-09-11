<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractEntity;
use Core\Entity\EntityInterface;
use Core\Entity\FileEntity;
use Core\Entity\FileEntityInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an Organization
 * 
 * @ODM\EmbeddedDocument
 */
class OrganizationContact extends AbstractEntity
{   
	
    /** @var string 
     * @ODM\String */
    protected $houseNumber;
    
    /** @var string 
     * @ODM\String */
    protected $postalcode;

    /** @var string 
     * @ODM\String */
    protected $city;
    
    /** @var string 
     * @ODM\String */
    protected $street;    
    
    
 
    /**
     * Sets the House Number of an Organization Address
     *
     * @return \Organizations\Entity\OrganizationContact
     */
    public function setHouseNumber($houseNumber = "")
    {
    	$this->houseNumber=$houseNumber;
    	return $this;
    }
    
    /** 
     * Gets the House Number of an Organization Address
     *
     * @return string
     */
    public function getHouseNumber()
    {
    	return $this->houseNumber;
    }
    
    /**
     * 
     * @return \Organizations\Entity\OrganizationContact
     */
    public function setPostalcode($postalcode) {
    	$this->postalcode = (String) $postalcode;
    	return $this;
    }

    /**
     * @return string
     */
    public function getPostalcode() {
    	return $this->postalcode;
    }
    
    /**
     * 
     * @return \Organizations\Entity\OrganizationContact
     */
    public function setCity($city = "") {
    	$this->city = (String) $city;
    	return $this;
    }

    /**
     * @return string
     */
    public function getCity() {
    	return $this->city;
    }
    
    /**
     * 
     * @return \Organizations\Entity\OrganizationContact
     */
    public function setStreet($street = "")
    {
    	$this->street=$street;
    	return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
    	return $this->street;
    }
    
}