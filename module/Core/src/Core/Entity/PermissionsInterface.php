<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** PermissionsInterface.php */ 
namespace Core\Entity;

interface PermissionsInterface 
{
    const PERMISSION_NONE   = 'none';
    const PERMISSION_VIEW   = 'view';
    const PERMISSION_CHANGE = 'change';
    const PERMISSION_ALL    = 'all';
    
    public function grant($resource, $permission);
    public function revoke($resource, $permission);
    public function clear();
    public function inherit(PermissionsInterface $permissions);
    public function isGranted($userOrId, $permission);
    public function isAssigned($resource);
    public function getFrom($resource);
    
}

