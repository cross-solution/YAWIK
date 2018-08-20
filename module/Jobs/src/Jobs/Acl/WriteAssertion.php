<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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

/**
 * This assertion checks permissions to change a job.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class WriteAssertion implements AssertionInterface
{
    /**
     * Returns true, if the user has write access to the job.
     *
     * {@inheritDoc}
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if (!$role instanceof UserInterface || !$resource instanceof JobInterface || 'edit' != $privilege) {
            return false;
        }

        /* @var $resource \Jobs\Entity\JobInterface */
        return $resource->getPermissions()->isGranted($role->getId(), Permissions::PERMISSION_CHANGE)
               || $this->checkOrganizationPermissions($role, $resource)
               || (null === $resource->getUser() && \Auth\Entity\UserInterface::ROLE_ADMIN == $role->getRole());
    }

    /**
     * Returns true, if the user has write access to the job granted from the organization.
     *
     * @param RoleInterface $role This must be a UserInterface instance
     * @param ResourceInterface $resource This must be a JobInterface instance
     *
     * @return bool
     */
    protected function checkOrganizationPermissions($role, $resource)
    {
        /* @var $resource \Jobs\Entity\JobInterface */
        /* @var $role     \Auth\Entity\UserInterface */
        $organization = $resource->getOrganization();
        if (!$organization) {
            return false;
        }

        if ($organization->isHiringOrganization()) {
            $organization = $organization->getParent();
        }

        $orgUser = $organization->getUser();

        if ($orgUser && $role->getId() == $orgUser->getId()) {
            return true;
        }

        $employees = $organization->getEmployees();

        foreach ($employees as $emp) {
            /* @var $emp \Organizations\Entity\EmployeeInterface */
            if ($emp->getUser()->getId() == $role->getId()
                && $emp->getPermissions()->isAllowed(EmployeePermissionsInterface::JOBS_CHANGE)
            ) {
                return true;
            }
        }

        return false;
    }
}
