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

use Core\Entity\DraftableEntityInterface;
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
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27.2 Fix checking of application drafts for the correct anonymous user.
 * @since 0.27 Checks, if application is a draft and only allow the associated user if so.
 * @since 0.4
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

        /* @var $resource ApplicationInterface|DraftableEntityInterface */

        $permissions = $resource->getPermissions();

        /* If application is a draft, only the associated user may view and edit.
         * As an anonymous user is not saved with the entity, we need to check the 'change' permission.
         */
        if ($resource->isDraft()) {
            return $role === $resource->getUser() || $permissions->isGranted($role, PermissionsInterface::PERMISSION_CHANGE);
        }

        if (ApplicationInterface::PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD == $privilege) {
            // only applicant is allowed to upload subsequent attachments
            return $permissions->isAssigned($role) && $permissions->isGranted($role, PermissionsInterface::PERMISSION_VIEW);
        }
        
        $permission = 'read' == $privilege ? PermissionsInterface::PERMISSION_VIEW : PermissionsInterface::PERMISSION_CHANGE;
        return $permissions->isGranted($role, $permission);
    }
}
