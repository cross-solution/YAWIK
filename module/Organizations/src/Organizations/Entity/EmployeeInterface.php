<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

use Auth\Entity\UserInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
interface EmployeeInterface 
{

    /**
     * Sets the user entity associated with this employee
     *
     * @param UserInterface $user
     *
     * @return self
     */
    public function setUser(UserInterface $user);

    /**
     * Gets the user entity.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Sets the permissions manager.
     *
     * @param EmployeePermissionsInterface $permissions
     *
     * @return self
     */
    public function setPermissions(EmployeePermissionsInterface $permissions);

    /**
     * Gets the permissions manager.
     *
     * @return EmployeePermissionsInterface
     */
    public function getPermissions();

    /**
     * Returns the state of the pending flag.
     *
     * @return boolean
     */
    public function isPending();

    /**
     * Sets the state of the pending flag.
     *
     * @param boolean $flag
     * @return self
     */
    public function setIsPending($flag);
}