<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** JobAccessAssertion.php */ 
namespace Jobs\Acl;

use Organizations\Entity\EmployeePermissionsInterface;
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

        /* @var $resource \Jobs\Entity\JobInterface */
        return $resource->getPermissions()->isGranted($role->getId(), Permissions::PERMISSION_CHANGE)
               || $this->checkOrganizationPermissions($role, $resource);
    }

    protected function checkOrganizationPermissions($role, $resource)
    {
        /* @var $resource \Jobs\Entity\JobInterface */
        /* @var $role     \Auth\Entity\UserInterface */
        $organization = $resource->getOrganization();
        if ($organization->isHiringOrganization()) {
            $organization = $organization->getParent();
        }

        if ($role->getId() == $organization->getUser()->getId()) {
            return true;
        }

        $employees = $organization->getEmployees();
        foreach ($employees as $emp) {
            /* @var $emp \Organizations\Entity\EmployeeInterface */
            if ($emp->getPermissions()->isAllowed($role, EmployeePermissionsInterface::JOBS_CHANGE)) {
                return true;
            }
        }

        return false;
    }
}