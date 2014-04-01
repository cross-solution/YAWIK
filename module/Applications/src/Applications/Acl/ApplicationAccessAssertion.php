<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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

class ApplicationAccessAssertion implements AssertionInterface
{
    /* (non-PHPdoc)
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(Acl $acl, 
                           RoleInterface $role = null, 
                           ResourceInterface $resource = null, 
                           $privilege = null) 
    {
        if (!$role instanceOf UserInterface || !$resource instanceOf ApplicationInterface) {
            return false;
        }
        
        if ($resource->dateCreated->getTimestamp() < 1396216800) { // 1396216800 = strtotime('2014-03-31');
            switch ($privilege) {
                case 'read':
                    return $this->assertRead($role, $resource)
                    || $this->assertWrite($role, $resource);
                    break;
            
                default:
                    return $this->assertWrite($role, $resource);
                    break;
            }
        } else {
            $permission = 'read' == $privilege ? PermissionsInterface::PERMISSION_VIEW : PermissionsInterface::PERMISSION_CHANGE;
            return $resource->getPermissions()->isGranted($role, $permission);
        }
    }
    
    protected function assertRead($role, $resource)
    {
        return $resource->getJob()->getUser()->getId() == $role->getId();
    }
    
    protected function assertWrite($role, $resource)
    {
        $job = $resource->getJob();
        return ($job && $role->getId() == $job->getUser()->getId());
    }
}