<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** GroupInterface.php */ 
namespace Auth\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\PermissionsInterface;

interface GroupInterface extends EntityInterface
{
    public function getName();
    public function setName($name);
    
    /*public function getUsers();
    public function setUsers($users);
    
    public function addUser($user);
    public function removeUser($user);
    */
}

