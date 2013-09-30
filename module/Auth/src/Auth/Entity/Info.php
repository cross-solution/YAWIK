<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Auth\Entity;

use Core\Entity\AbstractEntity;

/**
 * The user model
 */
class Info extends AbstractEntity implements InfoInterface
{   
    /** @var string */
    protected $_email;
    
    /** @var string */ 
    protected $_firstName;
    
    /** @var string */
    protected $_lastName;
    
    /** @var string */
    protected $_phone;
    
    /** @var string */
    protected $_postalcode;

    /** @var string */
    protected $_city;
    
    /** @var \Core\Entity\FileEntityInterface */
    protected $imageId;
    
    
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setEmail($email) {
        $this->_email = (String) $email;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getEmail() {
        return $this->_email;
    }

    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setFirstName($name)
    {
        $this->_firstName = trim((String)$name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLastName($name)
    {
        $this->_lastName = trim((String) $name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLastName()
    {
        return $this->_lastName;
    }
    
    public function getDisplayName()
    {
        if (!$this->lastName) {
            return $this->email;
        }
        return ($this->firstName ? $this->firstName . ' ' : '') . $this->lastName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setPhone($phone) {
    	$this->_phone = (String) $phone;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getPhone() {
    	return $this->_phone;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setPostalcode($postalcode) {
    	$this->_postalcode = (String) $postalcode;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getPostalcode() {
    	return $this->_postalcode;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setCity($city) {
    	$this->_city = (String) $city;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getCity() {
    	return $this->_city;
    }
    
    public function setImageId($imageId)
    {
        $this->imageId = (string) $imageId;
        return $this;
    }
    
    public function getImageId()
    {
        return $this->imageId;
    }
    
}