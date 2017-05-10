<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Acl\Listener;

use Acl\Assertion\AssertionEvent;
use Auth\Entity\UserInterface;
use Organizations\Entity\EmployeePermissionsInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Checks if an user may create jobs according to the organization permissions.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class CheckJobCreatePermissionListener
{

    /**
     * Attach one or more listeners
     * Implementors may add an optional $priority argument; the SharedEventManager
     * implementation will pass this to the aggregate.
     *
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('Jobs/Acl/Assertion/Create', AssertionEvent::EVENT_ASSERT, array($this, 'checkCreatePermission'));
    }

    /**
     * Detach all previously attached listeners
     *
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        // not used.
    }

    /**
     * Checks if the user may create jobs according to the organization permissions.
     *
     * @param AssertionEvent $e
     *
     * @return bool
     */
    public function checkCreatePermission(AssertionEvent $e)
    {
        /* @var $role \Auth\Entity\User
         * @var $organization \Organizations\Entity\OrganizationReference
         */
        $role = $e->getRole();

        if (!$role instanceof UserInterface) {
            return false;
        }

        $organization = $role->getOrganization();

        if (!$organization->hasAssociation()
            || $organization->isOwner()
        ) {
            return true;
        }

        $employees = $organization->getEmployees();

        foreach ($employees as $emp) {
            /* @var $emp \Organizations\Entity\EmployeeInterface */
            if ($emp->getUser()->getId() == $role->getId()
                && $emp->getPermissions()->isAllowed(EmployeePermissionsInterface::JOBS_CREATE)
            ) {
                return true;
            }
        }

        return false;
    }
}
