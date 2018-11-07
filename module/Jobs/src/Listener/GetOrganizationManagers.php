<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Core\Listener\Events\AjaxEvent;
use Organizations\Entity\EmployeeInterface;
use Organizations\Repository\Organization as OrganizationRepository;

/**
 * Gets list of department managers for job form manager select element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class GetOrganizationManagers
{

    /**
     * @var OrganizationRepository
     */
    private $repository;

    /**
     * @param OrganizationRepository $repository
     */
    public function __construct(OrganizationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle ajax event.
     *
     * @param AjaxEvent $event
     *
     * @return array
     */
    public function __invoke(AjaxEvent $event)
    {
        $query = $event->getRequest()->getQuery();
        $orgId = $query->get('organization');

        /* @var \Organizations\Entity\Organization $org */
        if (!$orgId || !($org = $this->repository->find($orgId))) {
            return ['status' => 'fail', 'error' => $orgId ? 'no organization found.' : 'missing organization id'];
        }

        if ($org->isHiringOrganization()) {
            $org = $org->getParent();
        }

        $workflowSettings = $org->getWorkflowSettings();

        if (!$workflowSettings->getAcceptApplicationByDepartmentManager()
            || !$workflowSettings->getAssignDepartmentManagersToJobs()
        ) {
            return ['status' => 'disabled'];
        }

        $managers = array();
        foreach ($org->getEmployeesByRole(EmployeeInterface::ROLE_DEPARTMENT_MANAGER) as $employee) {
            /* @var EmployeeInterface $employee */
            $user = $employee->getUser();
            $managers[] = [
                'id' => $user->getId(),
                'name' => $user->getInfo()->getDisplayName(),
                'email' => $user->getInfo()->getEmail(),
            ];
        }

        return ['status' => 'ok', 'managers' => $managers];
    }
}
