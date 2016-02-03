<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsInterface.php */
namespace Core\Entity;

/**
 * Interface for a permissions manager of an entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface PermissionsInterface
{
    /**#@+
     * Permission name constants.
     * @var string
     */
    const PERMISSION_NONE   = 'none';
    const PERMISSION_VIEW   = 'view';
    const PERMISSION_CHANGE = 'change';
    const PERMISSION_ALL    = 'all';
    /**#@-*/

    /**
     * Grants a permission to a user or resource.
     *
     * You may pass either a user id, a user entity or any object implementing the
     * {@link PermissionsResourceInterface}.
     *
     * If you pass <i>null</i> as $permission, and a PermissionsResourceInterface as resource,
     * the resources' {@link PermissionsResourceInterface::getPermissionsUserIds()} method is called with
     * an additional parameter $type, with the value of {@link $type}. In this case however, the resources' method
     * must return a specification array instead of a simple list of userIds. This specification array must have the
     * format
     * <pre>
     * array(
     *      permission => array(userId, ...),
     *      ...
     * )</pre>
     *
     * @param string|\Auth\Entity\UserInterface|PermissionsResourceInterface $resource
     * @param string|null $permission
     *
     * @return self
     */
    public function grant($resource, $permission = null);

    /**
     * Revokes a permission from a user or resource.
     *
     * @param             $resource
     * @param string|null $permission
     *
     * @return self
     */
    public function revoke($resource, $permission = null);

    /**
     * Clears all permissions.
     *
     * @return self
     */
    public function clear();

    /**
     * Inherits permissions from another permissions manager.
     *
     * @param PermissionsInterface $permissions
     *
     * @return self
     */
    public function inherit(PermissionsInterface $permissions);

    /**
     * Checks, if a user is granted a specific permission.
     *
     * @param string|\Auth\Entity\UserInterface $userOrId
     * @param string $permission
     *
     * @return boolean
     */
    public function isGranted($userOrId, $permission);

    /**
     * Checks, if a resource is assigned.
     *
     * @param string|\Auth\Entity\UserInterface|PermissionsResourceInterface $resource
     *
     * @return boolean
     */
    public function isAssigned($resource);

    /**
     * Gets the permission granted to a resource.
     *
     * @param string|\Auth\Entity\UserInterface|PermissionsResourceInterface $resource
     *
     * @return string|null
     */
    public function getFrom($resource);

    /**
     * Returns true, if the managed permissions has changed.
     *
     * @return boolean
     */
    public function hasChanged();
}
