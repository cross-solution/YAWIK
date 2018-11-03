<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
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
 */
class WriteAssertion implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        return 'edit' == $privilege
               && $role instanceof UserInterface
               && $resource instanceof OrganizationInterface
               && $resource->getPermissions()->isGranted($role, PermissionsInterface::PERMISSION_CHANGE);
    }
}
