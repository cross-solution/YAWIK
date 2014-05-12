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

use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\EntityInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Defines an user model interface 
 */
interface UserInterface extends IdentifiableEntityInterface, RoleInterface
{
    
    /**
     * Sets the users login name
     *
     * @param string $name
     */
    public function setLogin($login);
    
    /**
     * Gets the users login name
     *
     * @return string
     */
    public function getLogin();
    
    /**
     * Sets the role of the users
     * 
     * @param unknown $role
     */
    public function setRole($role);

    /**
     * Gets the role of the user
     */
    public function getRole();
    
    /**
     * Set contact data, user image etc. of a user.
     * 
     * @param InfoInterface $info
     */
    public function setInfo(InfoInterface $info);
    
    /**
     * Get contact data, user image etc. of a user.
     */
    public function getInfo();    
    
    /**
     * Set the API password of the user.
     * 
     * @param String $password
     */
    public function setPassword($password);
    
    /**
     * get the Webfrontend password of the user
     * 
     * @param String $credential
     */
    public function setCredential($credential);
    
    /**
     * get the Webfrontend password of the user
     */
    public function getCredential();
    
    /**
     * Sets the profile info from Hybridauth
     * 
     * @param array $profile
     */
    public function setProfile(array $profile);
    
    /**
     * Gets the profile info from Hybridauth
     * 
     * @return array
     */
    public function getProfile();
    
    /**
     * get user settings of a certain Module.
     * 
     * @param String $module
     */
    public function getSettings($module);

    /**
     * get groups of the user
     */
    public function getGroups();
    
    
}