<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth model */
namespace Auth\Model;

use Core\Model\ModelInterface;

/**
 * User model interface 
 */
interface UserInterface extends ModelInterface
{
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
     * Sets the display name
     *
     * @param string $name
     */
    public function setDisplayName($name);
    
    /**
     * Gets the display name
     *
     * @return string
     */
    public function getDisplayName();
    
    /**
     * Sets the Facebook info from Hybridauth
     * 
     * @param array $info
     */
    public function setFacebookInfo(array $info);
    
    /**
     * Gets the Facebook info from Hybridauth
     * 
     * @return array
     */
    public function getFacebookInfo();
    
    /**
     * Sets the LinkedIn info from Hybridauth
     *
     * @param array $info
     */
    public function setLinkedInInfo(array $info);
    
    /**
     * Gets the LinkedIn info from Hybridauth
     *
     * @return array
     */
    public function getLinkedInInfo();
    
    /**
     * Sets the Xing info from Hybridauth
     * 
     * @param array $info
     */
    public function setXingInfo(array $info);
    
    /**
     * Gets the Xing info from Hybridauth
     *
     * @return array
     */
    public function getXingInfo();
}