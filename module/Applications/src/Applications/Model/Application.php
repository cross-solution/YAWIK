<?php

namespace Applications\Model;

use Core\Model\AbstractDateFormatEnabledModel;

/**
 * @todo write interface
 * @author mathias
 *
 */
class Application extends AbstractDateFormatEnabledModel implements ApplicationInterface
{
    protected $jobId;
    protected $dateCreated;
    protected $dateModified;
    protected $title;
    protected $firstname;
    protected $lastname;
    protected $street;
    protected $houseNumber;
    protected $zipCode;
    protected $location;
    protected $phoneNumber;
    protected $mobileNumber;
    protected $email;
    protected $skills;

    /**
     * @return the $jobId
     */
    public function getJobId ()
    {
        return $this->jobId;
    }

	/**
     * @param field_type $jobId
     */
    public function setJobId ($jobId)
    {
        $this->jobId = $jobId;
    }

    public function getDateCreated ($format=null)
    {
        if (!$this->dateCreated) {
            $this->setDateCreated('now');
        }
        return null !== $format
            ? strftime($format, $this->dateCreated->getTimestamp())
            : $this->dateCreated;
    }
    
    public function setDateCreated ($dateCreated)
    {
        if (is_string($dateCreated)) {
            $dateCreated = new \DateTime($dateCreated);
        }
        
        if (!$dateCreated instanceOf \DateTime) {
            $dateCreated = new \DateTime();
        }
        
        $this->dateCreated = $dateCreated;
    }
    
    public function getDateModified ($format=null)
    {
        if (!$this->dateModified) {
            $this->setDateModified('now');
        }
        return null !== $format
            ? $this->dateModified->format($format)
            : $this->dateModified;
    }
    
    public function setDateModified ($dateModified)
    {
        if (is_string($dateModified)) {
            $dateCreated = new \DateTime($dateModified);
        }
    
        if (!$dateModified instanceOf \DateTime) {
            $dateModified = new \DateTime();
        }
    
        $this->dateModified = $dateModified;
    }
    
	/**
     * @return the $title
     */
    public function getTitle ()
    {
        return $this->title;
    }

	/**
     * @param field_type $title
     */
    public function setTitle ($title)
    {
        $this->title = $title;
    }

	/**
     * @return the $firstname
     */
    public function getFirstname ()
    {
        return $this->firstname;
    }

	/**
     * @param field_type $firstname
     */
    public function setFirstname ($firstname)
    {
        $this->firstname = $firstname;
    }

	/**
     * @return the $lastname
     */
    public function getLastname ()
    {
        return $this->lastname;
    }

	/**
     * @param field_type $lastname
     */
    public function setLastname ($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getName()
    {
        return ( ($firstname = $this->getFirstname()) ? "$firstname " : "")
             . $this->getLastname();
    } 
    
	/**
     * @return the $street
     */
    public function getStreet ()
    {
        return $this->street;
    }

	/**
     * @param field_type $street
     */
    public function setStreet ($street)
    {
        $this->street = $street;
    }

	/**
     * @return the $housenumber
     */
    public function getHouseNumber ()
    {
        return $this->houseNumber;
    }

	/**
     * @param field_type $housenumber
     */
    public function setHouseNumber ($housenumber)
    {
        $this->houseNumber = $housenumber;
    }

	/**
     * @return the $zipCode
     */
    public function getZipCode ()
    {
        return $this->zipCode;
    }

	/**
     * @param field_type $zipCode
     */
    public function setZipCode ($zipCode)
    {
        $this->zipCode = $zipCode;
    }

	/**
     * @return the $location
     */
    public function getLocation ()
    {
        return $this->location;
    }

	/**
     * @param field_type $location
     */
    public function setLocation ($location)
    {
        $this->location = $location;
    }

	/**
     * @return the $phoneNumber
     */
    public function getPhoneNumber ()
    {
        return $this->phoneNumber;
    }

	/**
     * @param field_type $phoneNumber
     */
    public function setPhoneNumber ($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

	/**
     * @return the $mobileNumber
     */
    public function getMobileNumber ()
    {
        return $this->mobileNumber;
    }

	/**
     * @param field_type $mobileNumber
     */
    public function setMobileNumber ($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

	/**
     * @return the $email
     */
    public function getEmail ()
    {
        return $this->email;
    }

	/**
     * @param field_type $email
     */
    public function setEmail ($email)
    {
        $this->email = $email;
    }

    /**
     * @return the $skills
     */
    public function getSkills()
    {
    	return $this->skills;
    }
    
    /**
     * @param field_type $skills
     * @return Application
     */
    public function setSkills($skills)
    {
    	$this->skills = $skills;
    	return $this;
    }
    
}


 class Contact {

 	protected $gender;
    protected $firstname;
    protected $lastname;
    protected $address;

    /**
     * @param string $gender
     */
    public function setGender($gender) {
    	$this->gender=$gender;
    }
    public function getGender(){
    	return $this->gender;
    }
    
	/**
	 * @param string $lastname
	 */
	public function setFirstname($lastname) {
		$this->firstname=$lastname;
	}
	public function getFirstname(){
		return $this->firstname;
	}
	/**
	 * @return string
	 */
	public function getLastname(){
		return $this->lastname;
	}
	public function setLastname($lastname){
		$this->lastname=$lastname;
	}
	
	/**
	 * @param object Address
	 * @return Contact
	 */	
	public function setAdress($address){
	    $this->address=$address;
	    return $this;	
	}
	/**
	 * @return Address
	 */
	public function getAddress(){
		return $this->address;
	}
}

class Address {

	protected $street;
	protected $houseNumber;
	protected $zipCode;
	protected $location;

	/**
	 * @param string $street
	 */
	public function setStreet($street){
		$this->street=$street;
	}
	public function getStreet($street){
	    return $this->street;
	}
	
	public function setHouseNumber($houseNumber){
		$this->houseNumber=$houseNumber;
	}
	public function getHouseNumber(){
		return $this->houseNumber;
	}
	
	public function setZipCode($zipCode){
		$this->zipCode=$zipCode;
	}
	public function getZipCode(){
		return $this->zipCode;
	}
	
	public function setLocation($location){
		$this->location=$location;
	}
	public function getLocation(){
		return $this->location;
	}
}