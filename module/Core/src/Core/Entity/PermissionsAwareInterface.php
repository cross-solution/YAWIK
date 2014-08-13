<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsAwareInterface.php */ 
namespace Core\Entity;

interface PermissionsAwareInterface
{
    
    public function getPermissions();
    public function setPermissions(PermissionsInterface $permissions);
}

