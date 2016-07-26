<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsAwareInterface.php */
namespace Core\Entity;

interface PermissionsAwareInterface
{
    /**
     * Gets the permissions entity.
     *
     * @return PermissionsInterface
     */
    public function getPermissions();

    /**
     * Sets the permissions entity.
     *
     * @param PermissionsInterface $permissions
     *
     * @return self
     */
    public function setPermissions(PermissionsInterface $permissions);
}
