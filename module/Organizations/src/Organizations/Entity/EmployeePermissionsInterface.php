<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

use Core\Entity\EntityInterface;

/**
 * Interface for employees' permissions.
 *
 * Defines the permission bit mask flags.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
interface EmployeePermissionsInterface extends EntityInterface
{
    /**#@+
     * Permissions bitmask constants
     *
     * @var int
     */
    const JOBS_VIEW           = 16;      //  10000
    const JOBS_CREATE         = 24;      //  11000  # Create w/o View makes no sense
    const JOBS_CHANGE         = 20;      //  10100  # Change w/o view makes no sense
    const APPLICATIONS_VIEW   = 2;       //  00010
    const APPLICATIONS_CHANGE = 3;       //  00011  # change w/o view makes no sense
    const ALL                 = 31;      //  11111
    const NONE                = 0;       //  00000
    /**#@- */

    /**
     * Sets the permissions bit mask directly.
     *
     * @param int $bitmask Valid values are any combination of Permission flags.
     *
     * @return self
     */
    public function setPermissions($bitmask);

    /**
     * Gets the permissions bit mask.
     *
     * @return int
     */
    public function getPermissions();

    /**
     * Grants one or more permissions.
     *
     * To grant more than one permission at once, either pass an array or
     * call this method with additional arguments (which must be intergers corresponding to
     * an permssissions bit mask flag.
     *
     * @param int|array $permission
     *
     * @return self
     */
    public function grant($permission);

    /**
     * Revokes one or more permissions.
     *
     * @see grant()
     *
     * @param int|array $permission
     *
     * @return self
     */
    public function revoke($permission);

    /**
     * Grants all permissions.
     *
     * @return self
     */
    public function grantAll();

    /**
     * Revokes all permissions.
     *
     * @return self
     */
    public function revokeAll();

    /**
     * Checks if a specific permission is granted.
     *
     * @param int $permission
     *
     * @return boolean
     */
    public function isAllowed($permission);
}
