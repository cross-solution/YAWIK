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

interface PermissionsResourceInterface 
{
    public function getPermissionsResourceId();
    public function getPermissionsUserIds();
}

