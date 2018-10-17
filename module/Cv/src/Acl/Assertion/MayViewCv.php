<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Cv\Acl\Assertion;

use Cv\Entity\Cv;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;
use Cv\Entity\CvInterface;
use Cv\Entity\Status;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class MayViewCv implements AssertionInterface
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
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
        /* @var ResourceInterface|Cv $resource */
        return $role instanceof UserInterface
               && $resource instanceof CvInterface
               && 'view' == $privilege
               && ($resource->getPermissions()->isGranted($role, PermissionsInterface::PERMISSION_VIEW)
                   || Status::PUBLIC_TO_ALL == (string) $resource->getStatus());
    }
}
