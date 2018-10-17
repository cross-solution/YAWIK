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

use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * Organization employee
 *
 * Basically a mapping to an user with additional permissions
 * specifical to the organization (and its jobs and applications).
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class Employee extends AbstractEntity implements EmployeeInterface
{
    /**
     * The user entity
     *
     * @var \Auth\Entity\UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id")
     */
    protected $user;

    /**
     * The permissions the user has for all organization relevant entities.
     *
     * @var EmployeePermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\EmployeePermissions")
     */
    protected $permissions;

    /**
     * Employee role.
     *
     * @var EmployeePermissionsInterface
     * @ODM\Field(type="string")
     */
    protected $role;

    /**
     * Status of this employees' association to this organization
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $status = self::STATUS_ASSIGNED;

    /**
     * Creates an instance.
     *
     * @param UserInterface $user
     * @param null|int|EmployeePermissionsInterface $permissions Passing an int means passing the permissions bitmask
     *                                                           which is passed along to the constructor of a new
     *                                                           instance of EmployeePermissionsInterface
     *
     * @since 0.19
     */
    public function __construct(UserInterface $user = null, $permissions = null)
    {
        if (null !== $user) {
            $this->setUser($user);

            if (is_int($permissions)) {
                $permissions = new EmployeePermissions($permissions);
            }

            if ($permissions instanceof EmployeePermissionsInterface) {
                $this->setPermissions($permissions);
            }
        }
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setPermissions(EmployeePermissionsInterface $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getPermissions()
    {
        if (!$this->permissions) {
            $this->setPermissions(new EmployeePermissions($this->permissions));
        }

        return $this->permissions;
    }

    public function setStatus($status)
    {
        if (!defined('self::STATUS_' . strtoupper($status))) {
            $status = self::STATUS_ASSIGNED;
        }

        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the Role of an Employee
     *
     * @since 0.25
     * @param $role
     */
    public function setRole($role)
    {
        $this->role=$role;
    }

    /**
     * Gets the Role of an employee
     *
     * @since 0.25
     * @return EmployeePermissionsInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    public function isAssigned()
    {
        return self::STATUS_ASSIGNED == $this->getStatus();
    }

    public function isPending()
    {
        return self::STATUS_PENDING == $this->getStatus();
    }

    public function isRejected()
    {
        return self::STATUS_REJECTED == $this->getStatus();
    }

    public function isUnassigned($strict = false)
    {
        return $strict
               ? self::STATUS_UNASSIGNED == $this->getStatus()
               : self::STATUS_ASSIGNED != $this->getStatus();
    }
}
