<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth model */
namespace Auth\Entity;

use Core\Entity\EntityInterface;

/**
 * User model interface 
 */
interface InfoInterface extends EntityInterface
{
    
	/**
	 * Sets the Day of the date of birth.
	 *
	 * @param string $birthDay
	 */
	public function setBirthDay($birthDay);
	
	/**
	 * Gets the Day of the date of birth
	 *
	 * @return string
	*/
	public function getBirthDay();
	
	/**
	 * Sets the month of the date of birth.
	 *
	 * @param string $birthMonath
	 */
	public function setBirthMonth($birthMonth);
	
	/**
	 * Gets the month of the date of birth
	 *
	 * @return string
	*/
	public function getBirthMonth();

	/**
	 * Sets the year of the date of birth.
	 *
	 * @param string $email
	 */
	public function setBirthYear($email);
	
	/**
	 * Gets the Year of the date of birth.
	 *
	 * @return string
	*/
	public function getBirthYear();
	
    /**
     * Sets the email.
     * 
     * @param string $email
     */
    public function setEmail($email);
    
    /**
     * Gets the email
     * 
     * @return string
     */
    public function getEmail();
    
    /**
     * Sets the first name
     * 
     * @param string $name
     */
    public function setFirstName($name);
    
    /**
     * Gets the first name
     *
     * @return string
     */
    public function getFirstName();
    
    /**
     * Sets the gender
     *
     * @param string $name
     */
    public function setGender($gender);
    
    /**
     * Gets the gender
     *
     * @return string
    */
    public function getGender();
    
    
    /**
     * Sets the last name
     *
     * @param string $name
     */
    public function setLastName($name);
    
    /**
     * Gets the last name
     *
     * @return string
     */
    public function getLastName();
    
    public function setImage(EntityInterface $image=null);
    public function getImage();
    
    /**
     * Sets the users street
     *
     * @param string $name
     */
    public function setStreet($name);
    
    /**
     * Gets the users street
     *
     * @return string
    */
    public function getStreet();
    
    /**
     * Sets the users house number
     *
     * @param string $houseNumber
     */
    public function setHouseNumber($houseNumber);
    
    /**
     * Gets the users house number
     *
     * @@return string
     */
    public function getHouseNumber();
    
    
    
    
}  