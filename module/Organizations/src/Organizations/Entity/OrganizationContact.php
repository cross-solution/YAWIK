<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an organization
 * 
 * @ODM\EmbeddedDocument
 */
class OrganizationContact extends AbstractIdentifiableHydratorAwareEntity
{   
	
    /**
     * BuildingNumber of an organization address
     *
     * @var string
     * @ODM\String */
    protected $houseNumber;
    
    /**
     * Postalcode of an organization address
     *
     * @var string
     * @ODM\String */
    protected $postalcode;

    /**
     * Cityname of an organization address
     *
     * @var string
     * @ODM\String */
    protected $city;
    
    /**
     * Streetname of an organization address
     *
     * @var string
     * @ODM\String */
    protected $street;

    /**
     * Sets the Buildingnumber of an organization address
     *
     * @param string $houseNumber
     * @return OrganizationContact
     */
    public function setHouseNumber($houseNumber = "")
    {
    	$this->houseNumber=$houseNumber;
    	return $this;
    }
    
    /** 
     * Gets the Buildingnumber of an organization address
     *
     * @return string
     */
    public function getHouseNumber()
    {
    	return $this->houseNumber;
    }

    /**
     * Sets a postal code of an organization address
     *
     * @param $postalcode
     * @return OrganizationContact
     */
    public function setPostalcode($postalcode) {
    	$this->postalcode = (String) $postalcode;
    	return $this;
    }

    /**
     * Gets a postal code of an organization address
     *
     * @return string
     */
    public function getPostalcode() {
    	return $this->postalcode;
    }
    
    /**
     * Sets a city (name) of an organization address
     *
     * @param string $city
     * @return OrganizationContact
     */
    public function setCity($city = "") {
    	$this->city = (String) $city;
    	return $this;
    }

    /**
     * Gets a city (name) of an organization address
     *
     * @return string
     */
    public function getCity() {
    	return $this->city;
    }
    
    /**
     * Sets a street name of an organization address
     *
     * @param string $street
     * @return OrganizationContact
     */
    public function setStreet($street = "")
    {
    	$this->street=$street;
    	return $this;
    }

    /**
     * Gets a street name of an organization address
     *
     * @return string
     */
    public function getStreet()
    {
    	return $this->street;
    }
    
}