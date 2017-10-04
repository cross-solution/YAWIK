<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth model */
namespace Auth\Entity;

use Core\Entity\EntityInterface;

/**
 * Defines an users Info model interface. The Info model holds contact
 * data.
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
     * @param string $birthMonth
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
     * @return boolean
     */
    public function isEmailVerified();

    /**
     * @param bool $emailVerified
     * @return self
     */
    public function setEmailVerified($emailVerified);
    
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
     * @param string $gender
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
    
    /**
     * Sets the profile Image of an user
     *
     * @param EntityInterface $image
     */
    public function setImage(EntityInterface $image = null);
    
    /**
     * Gets the profile Image of an user
     *
     * @return \Auth\Entity\UserImage
     */
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

    /**
     * Gets the user display name
     *
     * @param bool $emailIfEmpty If true, returns the email address, if no last name is set.
     *
     * @return string
     * @since 0.19 added parameter $emailIfEmpty
     */
    public function getDisplayName($emailIfEmpty = true);

    /**
     * Sets the users postal Code
     *
     * @param string $postalCode
     * @since 0.20
     */
    public function setPostalCode($postalCode);

    /**
     * Gets the users postal Code
     *
     * @since 0.20
     * @return string
     */
    public function getPostalCode();

    /**
     * Sets the users phone number
     *
     * @param string $phone
     * @since 0.20
     */
    public function setPhone($phone);

    /**
     * Gets the users phone number
     *
     * @since 0.20
     * @return string
     */
    public function getPhone();

    /**
     * Sets the users city
     *
     * @param string $city
     * @since 0.20
     */
    public function setCity($city);

    /**
     * Gets the users city
     *
     * @since 0.20
     * @return string
     */
    public function getCity();

    /**
     * Sets the users country
     *
     * @param string $country
     * @since 0.30
     */
    public function setCountry($country);

    /**
     * Gets the users country
     *
     * @since 0.30
     * @return string
     */
    public function getCountry();
}
