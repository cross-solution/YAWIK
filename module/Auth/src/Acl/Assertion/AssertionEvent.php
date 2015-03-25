<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Acl\Assertion;

use Zend\EventManager\Event;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AssertionEvent extends Event
{
    /**
     * Events
     */
    const EVENT_ASSERT = 'assert';

    /**
     * The acl instance
     *
     * @var Acl
     */
    protected $acl;

    /**
     * The role.
     *
     * @var RoleInterface
     */
    protected $role;

    /**
     * The resource.
     *
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * The privilege.
     *
     * @var string
     */
    protected $privilege;

    protected $name = self::EVENT_ASSERT;

    /**
     * Sets the acl instance.
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     *
     * @return self
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Gets the acl instance.
     *
     * @return \Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Sets the privilege name.
     *
     * @param string $privilege
     *
     * @return self
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * Gets the privilege name.
     *
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * Sets the resource.
     *
     * @param \Zend\Permissions\Acl\Resource\ResourceInterface $resource
     *
     * @return self
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Gets the resource.
     *
     * @return \Zend\Permissions\Acl\Resource\ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Sets the role.
     *
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     *
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Gets the role.
     *
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

}