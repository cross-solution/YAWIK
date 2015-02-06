<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileAccessAssertion.php */ 
namespace Applications\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Applications\Entity\ApplicationInterface;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;

/**
 * Checks permission on attachments
 */
class ApplicationAccessAssertion implements AssertionInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     * @param string $privilege
     * @return bool
     */
    public function assert(Acl $acl, 
                           RoleInterface $role = null, 
                           ResourceInterface $resource = null, 
                           $privilege = null) 
    {
        if (!$role instanceOf UserInterface || !$resource instanceOf ApplicationInterface) {
            return false;
        }
        
        $permission = 'read' == $privilege ? PermissionsInterface::PERMISSION_VIEW : PermissionsInterface::PERMISSION_CHANGE;
        return $resource->getPermissions()->isGranted($role, $permission);
    }
    
    /**
     * Checks read access on attachments
     * 
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @return boolean
     */
    protected function assertRead($role, $resource)
    {
        return $resource->getJob()->getUser()->getId() == $role->getId();
    }
    
    /**
     * Checks write Access on attachments
     * 
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @return boolean
     */
    protected function assertWrite($role, $resource)
    {
        $job = $resource->getJob();
        return ($job && $role->getId() == $job->getUser()->getId());
    }
}