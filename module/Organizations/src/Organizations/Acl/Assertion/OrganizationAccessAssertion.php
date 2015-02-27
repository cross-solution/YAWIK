<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Acl\Assertion;

use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\OrganizationInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @since 0.18
 */
class OrganizationAccessAssertion implements AssertionInterface
{
    /**
     * Returns true if and only if the assertion conditions are met
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl               $acl
     * @param  RoleInterface     $role
     * @param  ResourceInterface $resource
     * @param  string            $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        if (!$role instanceOf UserInterface || !$resource instanceOf OrganizationInterface) {
            return false;
        }

        /* @var $resource OrganizationInterface */

        $permission = 'read' == $privilege ? PermissionsInterface::PERMISSION_VIEW : PermissionsInterface::PERMISSION_CHANGE;
        return $resource->getPermissions()->isGranted($role, $permission);
    }
}