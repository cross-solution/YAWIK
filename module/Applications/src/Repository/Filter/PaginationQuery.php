<?php
/**
 * YAWIK
 *
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Applications\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Doctrine\MongoDB\Query\Builder;
use Laminas\Stdlib\Parameters;
use Organizations\Entity\EmployeeInterface;

/**
 * maps query parameters to entity attributes
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @since 0.29.2 [mg] - modified to match new ApplicationsFilter form parameters.
 */
class PaginationQuery extends AbstractPaginationQuery
{
    /**
     * Repository to query
     *
     * @var String
     */
    protected $repositoryName="Applications/Application";

    /**
     * Sortable fields
     *
     * @var array
     */
    protected $sortPropertiesMap = array(
        'date' => 'dateCreated.date',
    );

    /**
     * Constructs pagination query.
     *
     * @param \Auth\AuthenticationService $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    /**
     * Creates a query for filtering applications
     * @see \Core\Repository\Filter\AbstractPaginationQuery::createQuery()
     * @param array $params
     * @param Builder $queryBuilder
     * @return Builder
     */
    public function createQuery($params, $queryBuilder)
    {
        $user = $this->auth->getUser();
        $userID = $user->getId();
        if ($params instanceof Parameters) {
            $value = $params->toArray();
        } else {
            $value = $params;
        }

        if (isset($value['job']) && !empty($value['job'])) {
            $queryBuilder->field('job')->equals($value['job']);
        }

        if (isset($value['unread']) && !empty($value['unread'])) {
            $queryBuilder->field('readBy')->notEqual($userID);
        }

        if (isset($value['q']) && !empty($value['q'])) {
            $search = strtolower($value['q']);
            $searchPatterns = array();

            foreach (explode(' ', $search) as $searchItem) {
                $searchPatterns[] = new \MongoRegex('/^' . $searchItem . '/');
            }
            $queryBuilder->field('keywords')->all($searchPatterns);
        }

        /*
         * We only show applications to which the user has view permissions.
         * and which are not in draft mode
         */
        $queryBuilder->field('permissions.view')->equals($userID)
                     ->field('isDraft')->equals(false);

        if (!isset($value['sort'])) {
            $value['sort'] = '-date';
        }

        if (isset($value['status']) && 'all' != $value['status']) {
            $queryBuilder->field('status.name')->equals($value['status']);
        }

        if ($user->hasOrganization()) {
            $org = $user->getOrganization();
            if (!$org->isOwner()) {
                $org = $org->getOrganization();
                $settings = $org->getWorkflowSettings();
                if ($settings->hasActiveWorkflow() && $settings->getAssignDepartmentManagersToJobs()) {
                    $employee = $org->getEmployee($userID);
                    if ($employee->getRole() === EmployeeInterface::ROLE_DEPARTMENT_MANAGER) {
                        $queryBuilder->field('refs.jobManagers')->equals($userID);
                    }
                }
            }
        }

        $queryBuilder->sort($this->filterSort($value['sort']));

        return $queryBuilder;
    }
}
