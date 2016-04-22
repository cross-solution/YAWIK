<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Entity;

use Auth\Entity\UserInterface;

/**
 * Interface for the Employee entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.18
 */
interface EmployeeInterface
{

    /**#@+
     * Status constant.
     *
     * @var string
     * @since 0.19
     */
    const STATUS_ASSIGNED   = 'ASSIGNED';
    const STATUS_PENDING    = 'PENDING';
    const STATUS_REJECTED   = 'REJECTED';
    const STATUS_UNASSIGNED = 'UNASSIGNED';

    /**
     * defines the role of a recruiter
     */
    const ROLE_RECRUITER = 'recruiter';
    /**
     * defines the role of a department manager
     */
    const ROLE_DEPARTMENT_MANAGER = 'department manager';
    /**
     * defines the role of the management
     */
    const ROLE_MANAGEMENT = 'management';

    /**#@-*/

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
     * Sets the status of this employee association.
     *
     * @param string $status
     *
     * @return self
     * @since 0.19
     */
    public function setStatus($status);

    /**
     * Gets the status of this employee association.
     *
     * @return string
     * @since 0.19
     */
    public function getStatus();

    /**
     * Sets the role of an employee
     *
     * @param string $status
     *
     * @return self
     * @since 0.25
     */
    public function setRole($status);

    /**
     * Gets the role of the employee.
     *
     * @return string
     * @since 0.25
     */
    public function getRole();

    /**#@+
     * Returns true, if this association has the specific status.
     *
     * @return bool
     * @since 0.19
     */
    public function isAssigned();

    public function isPending();

    public function isRejected();

    public function isUnassigned();
    /**#@-*/
}
