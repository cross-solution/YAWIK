<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** GroupInterface.php */
namespace Auth\Entity;

use Core\Entity\IdentifiableEntityInterface;

/**
 * Defines an user group
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface GroupInterface extends IdentifiableEntityInterface
{
    
    /**
     * Gets the owner of this group.
     *
     * @return UserInterface
     */
    public function getOwner();
    
    /**
     * Sets the owner of this group.
     *
     * @param UserInterface $user
     * @return GroupInterface
     */
    public function setOwner(UserInterface $user);
    
    /**
     * Gets the name of the group.
     *
     * @return string
     */
    public function getName();
    
    /**
     * Sets the name of the group.
     *
     * @param string $name
     * @return GroupInterface
     */
    public function setName($name);
    
    /**
     * Gets the array of user ids assigned to this group.
     *
     * @return array
     */
    public function getUsers();
    
    /**
     * Sets the array of user ids assigned to this group.
     *
     * @param array $users
     * @return GroupInterface
     */
    public function setUsers(array $users);
}
