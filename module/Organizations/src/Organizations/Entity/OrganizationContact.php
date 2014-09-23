<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an Organization
 * 
 * @ODM\EmbeddedDocument
 */
class OrganizationContact extends AbstractIdentifiableHydratorAwareEntity
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
     * @param string $houseNumber
     * @return $this
     */
    public function setHouseNumber($houseNumber = "")
    {
    	$this->houseNumber=$houseNumber;
    	return $this;
    }
    
    /** 
     * Gets the House Number of an Organization Address
     * @return string
     */
    public function getHouseNumber()
    {
    	return $this->houseNumber;
    }

    /**
     * Sets a postal code
     * @param $postalcode
     * @return $this
     */
    public function setPostalcode($postalcode) {
    	$this->postalcode = (String) $postalcode;
    	return $this;
    }

    /**
     * Gets a postal code
     * @return string
     */
    public function getPostalcode() {
    	return $this->postalcode;
    }
    
    /**
     * Sets a city (name)
     * @param string $city
     * @return $this
     */
    public function setCity($city = "") {
    	$this->city = (String) $city;
    	return $this;
    }

    /**
     * Gets a city (name)
     * @return string
     */
    public function getCity() {
    	return $this->city;
    }
    
    /**
     * Sets a street name
     * @param string $street
     * @return $this
     */
    public function setStreet($street = "")
    {
    	$this->street=$street;
    	return $this;
    }

    /**
     * Gets a street name
     * @return string
     */
    public function getStreet()
    {
    	return $this->street;
    }
    
}