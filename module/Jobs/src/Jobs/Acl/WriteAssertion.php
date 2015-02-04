<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** JobAccessAssertion.php */ 
namespace Jobs\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Jobs\Entity\JobInterface;
use Auth\Entity\UserInterface;
use Core\Entity\Permissions;

class WriteAssertion implements AssertionInterface
{
    /* (non-PHPdoc)
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
    */
    public function assert(Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null)
    {
        if (!$role instanceOf UserInterface || !$resource instanceOf JobInterface || 'edit' != $privilege) {
            return false;
        }

        return $resource->getPermissions()->isGranted($role->getId(), Permissions::PERMISSION_CHANGE);
    }
}