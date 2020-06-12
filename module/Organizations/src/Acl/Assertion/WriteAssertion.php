<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Organizations\Acl\Assertion;

use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\OrganizationInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

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
