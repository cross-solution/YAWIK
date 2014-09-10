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
 * contact information 
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
     * 
     * @return \Organizations\Entity\Contact
     */
    public function setHouseNumber($houseNumber)
    {
    	$this->houseNumber=$houseNumber;
    	return $this;
    }
    
    /** 
     * 
     */
    public function getHouseNumber()
    {
    	return $this->houseNumber;
    }
    
    /**
     * 
     * @return \Organizations\Entity\Contact
     */
    public function setPostalcode($postalcode) {
    	$this->postalcode = (String) $postalcode;
    	return $this;
    }
    
    /**
     * 
     */
    public function getPostalcode() {
    	return $this->postalcode;
    }
    
    /**
     * 
     * @return \Organizations\Entity\Contact
     */
    public function setCity($city) {
    	$this->city = (String) $city;
    	return $this;
    }
    
    /**
     * 
     */
    public function getCity() {
    	return $this->city;
    }
    
    /**
     * 
     * @return \Organizations\Entity\Contact
     */
    public function setStreet($street)
    {
    	$this->street=$street;
    	return $this;
    }

    /**
     * 
     */
    public function getStreet() 
    {
    	return $this->street;
    }
    
}