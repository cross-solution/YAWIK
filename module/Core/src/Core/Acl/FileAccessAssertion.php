<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileAccessAssertion.php */ 
namespace Core\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Core\Entity\FileInterface;
use Auth\Entity\UserInterface;

class FileAccessAssertion implements AssertionInterface
{
    /* (non-PHPdoc)
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(Acl $acl, 
                           RoleInterface $role = null, 
                           ResourceInterface $resource = null, 
                           $privilege = null) 
    {
        if (!$role instanceOf UserInterface || !$resource instanceOf FileInterface) {
            return false;
        }
        
        $roleId = $role->getId();
        $userId = null !== $resource->getUser() ? $resource->getUser()->getId() : null;
        $allowedUsers = $resource->getAllowedUsers();
        
        return ($userId && $roleId == $userId) || $allowedUsers->contains($role);
    }
}