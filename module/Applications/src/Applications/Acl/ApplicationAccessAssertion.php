<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Applications\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Applications\Entity\ApplicationInterface;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;

/**
 * Checks permission on attachments
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ApplicationAccessAssertion implements AssertionInterface
{
    /**
     * Checks permissions based on resources' permissions.
     *
     * {@inheritDoc}
     *
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if (!$role instanceof UserInterface || !$resource instanceof ApplicationInterface) {
            return false;
        }
        /* @var $resource ApplicationInterface */
        $permissions = $resource->getPermissions();
        
        if (ApplicationInterface::PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD == $privilege) {
            // only applicant is allowed to upload subsequent attachments
            return $permissions->isAssigned($role) && $permissions->isGranted($role, PermissionsInterface::PERMISSION_VIEW);
        }
        
        $permission = 'read' == $privilege ? PermissionsInterface::PERMISSION_VIEW : PermissionsInterface::PERMISSION_CHANGE;
        return $permissions->isGranted($role, $permission);
    }
}
