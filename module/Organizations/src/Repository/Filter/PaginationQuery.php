<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Repository\Filter;

use Auth\AuthenticationService;
use Auth\Entity\UserInterface;
use Core\Repository\Filter\AbstractPaginationQuery;
use Jobs\Entity\StatusInterface;
use Organizations\Entity\Organization;
use Jobs\Repository\Job as JobRepository;
use Zend\Stdlib\Parameters;

/**
 * maps query parameters to entity attributes
 *
 * @package Organizations
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class PaginationQuery extends AbstractPaginationQuery
{
    const PAGE_TYPE_PROFILE = 'profile';
    const PAGE_TYPE_LIST    = 'list';

    /**
     * Repository to query
     *
     * @var String
     */
    protected $repositoryName="Organizations/Organization";
    
    /**
     * Sortable fields
     *
     * @var array
     */
    protected $sortPropertiesMap = array(
        'date' => 'dateCreated.date',
    );

    /**
     * @var JobRepository
     */
    protected $jobRepository;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * Constructs pagination query.
     *
     * @param JobRepository         $jobRepository
     * @param AuthenticationService $auth
     */
    public function __construct(
        JobRepository $jobRepository,
        AuthenticationService $auth
    ) {
        $this->jobRepository = $jobRepository;
        $this->authService = $auth;
    }
    
    /**
     * Creates a query for filtering organizations
     *
     * @see \Core\Repository\Filter\AbstractPaginationQuery::createQuery()
     * @param $params
     * @param \Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        if ($params instanceof Parameters) {
            $value = $params->toArray();
        } else {
            $value = $params;
        }

        /*
         * if user is recruiter or admin
         * filter query based on permissions.view
         */
        $auth = $this->authService;
        $user = $auth->getUser();
        $ignored = [null,'guest',UserInterface::ROLE_USER];
        if (!in_array($user->getRole(), $ignored)) {
            $queryBuilder->field('permissions.view')->equals($user->getId());
        }

        if (isset($params['q']) && $params['q'] && $params['q'] != 'en/organizations/profile') {
            $queryBuilder->text($params['q'])->language('none');
        }

        if (!isset($value['sort'])) {
            $value['sort'] = '-date';
        }

        $queryBuilder->sort($this->filterSort($value['sort']));

        if (isset($params['type']) && $params['type'] === 'profile') {
            //@TODO: we should use aggregate query here
            $queryBuilder->field('profileSetting')
                ->in([Organization::PROFILE_ALWAYS_ENABLE,Organization::PROFILE_ACTIVE_JOBS])
            ;

            $filters = $this->getOrganizationProfileFilters($queryBuilder);
            if (count($filters) > 0) {
                $queryBuilder->field('id')->notIn($filters);
            }
        }

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ODM\MongoDB\Query\Builder $queryBuilder $queryBuilder
     * @return array
     */
    private function getOrganizationProfileFilters($queryBuilder)
    {
        /* @var \Organizations\Entity\Organization $organization */
        $jobRepository = $this->jobRepository;
        $results = $queryBuilder->getQuery()->execute();

        $filters = [];
        foreach ($results->toArray() as $organization) {
            if ($organization->getProfileSetting()==Organization::PROFILE_ACTIVE_JOBS) {
                $qb = $jobRepository->createQueryBuilder();
                $qb
                    ->field('organization')->equals($organization->getId())
                    ->field('status.name')->notIn([StatusInterface::EXPIRED, StatusInterface::INACTIVE])
                    ->field('isDraft')->notEqual(true)
                ;
                $count = $qb->getQuery()->execute()->count();
                if ($count == 0) {
                    $filters[] = $organization->getId();
                }
            }
        }

        return $filters;
    }
}
