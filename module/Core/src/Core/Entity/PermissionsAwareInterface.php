<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** PermissionsAwareInterface.php */ 
namespace Core\Entity;

interface PermissionsAwareInterface
{
    
    public function getPermissions();
    public function setPermissions(PermissionsInterface $permissions);
}

