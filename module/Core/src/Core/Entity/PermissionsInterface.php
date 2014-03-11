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
    
    public function grantTo($userOrId, $permission);
    public function revokeFrom($userOrId, $permission);
    public function getFrom($userOrId);
    public function isGranted($userOrId, $permission);
    
}

