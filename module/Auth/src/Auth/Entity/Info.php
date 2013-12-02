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
use Core\Entity\EntityInterface;

/**
 * The user model
 */
class Info extends AbstractEntity implements InfoInterface
{   
	
	/** @var string */
	protected $birthDay;
	
	/** @var string */
	protected $birthMonth;

	/** @var string */
	protected $birthYear;
	
    /** @var string */
    protected $email;
    
    /** @var string */ 
    protected $firstName;
    
    /** @var string */
    protected $gender;
    
    /** @var string */
    protected $houseNumber;
    
    /** @var string */
    protected $lastName;
    
    /** @var string */
    protected $phone;
    
    /** @var string */
    protected $postalcode;

    /** @var string */
    protected $city;
    
    /** @var \Core\Entity\FileEntityInterface */
    protected $imageId;
    
    protected $image;
    
    /** @var string */
    protected $street;    
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setBirthDay($birthDay)
    {
    	$this->birthDay=$birthDay;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getBirthDay()
    {
    	return $this->street;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setBirthMonth($birthMonth)
    {
    	$this->birthDay=$birthMonth;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getBirthMonth()
    {
    	return $this->birthMonth;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setBirthYear($birthYear)
    {
    	$this->birthYear=$birthYear;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getBirthYear()
    {
    	return $this->birthYear;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setEmail($email)
    {
    	$this->email = trim((String)$email);
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getEmail()
    {
    	return $this->email;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setFirstName($firstName)
    {
    	$this->firstName = trim((String)$firstName);
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getGender()
    {
    	return $this->gender;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setGender($gender)
    {
    	$this->gender = trim((String)$gender);
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getFirstName()
    {
    	return $this->firstName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setHouseNumber($houseNumber)
    {
    	$this->houseNumber=$houseNumber;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getHouseNumber()
    {
    	return $this->houseNumber;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLastName($name)
    {
        $this->lastName = trim((String) $name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function getDisplayName()
    {
        if (!$this->lastName) {
            return $this->email;
        }
        return ($this->firstName ? $this->firstName . ' ' : '') . $this->lastName;
    }
    
    public function getAddress($extended = false)
    {
        $address = array();
        if ($this->lastName) {
            $address[] = ("male" == $this->gender ? 'Herr' : 'Frau') . ' '
                      . ($this->firstName ? $this->firstName . ' ' : '') 
                      . $this->lastName;
        }
        if ($this->street) {
            $address[] = $this->street . ($this->houseNumber ? ' ' . $this->houseNumber : '');
        }
        if ($this->city) {
            $address[] = ($this->postalCode ? $this->postalCode . ' ' : '') . $this->city;
        }
        
        if ($extended) {
            $address[] = ''; // empty line
        
            if ($this->phone) {
                $address[] = $this->phone;
            }
            if ($this->email) {
                $address[] = $this->email;
            }
        }    
        
        return implode(PHP_EOL, $address);
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setPhone($phone) {
    	$this->phone = (String) $phone;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getPhone() {
    	return $this->phone;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setPostalcode($postalcode) {
    	$this->postalcode = (String) $postalcode;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getPostalcode() {
    	return $this->postalcode;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setCity($city) {
    	$this->city = (String) $city;
    	return $this;
    }
    
    /** {@inheritdoc} */
    public function getCity() {
    	return $this->city;
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
    
    public function injectImage(EntityInterface $image)
    {
        $this->image = $image;
        return $this;
    }
    
    public function getImage()
    {
        return $this->image;
    }
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setStreet($street)
    {
    	$this->street=$street;
    	return $this;
    }

    /** {@inheritdoc} */
    public function getStreet() 
    {
    	return $this->street;
    }
    
}