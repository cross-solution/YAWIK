<?php
/**
 * Cross Applicant Management
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
        
        switch ($privilege) {
            case 'read':
                return $this->assertRead($role, $resource) 
                       || $this->assertWrite($role, $resource);
                break;

            default:
                return $this->assertWrite($role, $resource);
                break;
        }
    }
    
    protected function assertRead($role, $resource)
    {
        return $resource->getJob()->getUser()->getId() == $role->getId();
    }
    
    protected function assertWrite($role, $resource)
    {
        $job = $resource->getJob();
        return ($job && $role->getId() == $job->getUserId());
    }
}