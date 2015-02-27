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
 * @todo write test 
 */
class Employee extends AbstractEntity implements EmployeeInterface
{
    /**
     * The user entity
     *
     * @var \Auth\Entity\UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     */
    protected $user;

    /**
     * The permissions the user has for all organization relevant entities.
     *
     * @var EmployeePermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\EmployeePermissions")
     */
    protected $permissions;

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

    public function isPending()
    {
        return $this->pending;
    }

    public function setPending($flag)
    {
        $this->pending = (bool) $flag;

        return $this;
    }

}