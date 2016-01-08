<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Repository\Filter;

use Jobs\Entity\Status;
use \Doctrine\ODM\MongoDB\Query\Builder;
use \Zend\Stdlib\Parameters;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Auth\Entity\UserInterface;

/**
 * Class PaginationAdminQuery
 *
 * This is currently only used to list pending jobs for approval
 *
 * @package Jobs\Repository\Filter
 */
class PaginationAdminQuery extends PaginationQuery
{
    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @param $auth
     * @param $acl
     */
    public function __construct(AuthenticationService $auth, Acl $acl)
    {
        $this->auth = $auth;
        $this->acl = $acl;
    }

    /**
     * @param Parameters $params
     * @param Builder $queryBuilder
     *
     * @return Builder
     */
    public function createQuery($params, $queryBuilder)
    {
        $this->value = $params->toArray();

        if (isset($this->value['params']['status']) &&
            !empty($this->value['params']['status']))
        {
            if ($this->value['params']['status'] != 'all'){
                $queryBuilder->field('status.name')->equals($this->value['params']['status']);
            }
        }else{
            $queryBuilder->field('status.name')->equals(Status::CREATED);
        }

        if (isset($this->value['params']['companyId']) &&
            !empty($this->value['params']['companyId']))
        {
            $queryBuilder->field('organization')->equals(new \MongoId($this->value['params']['companyId']));
        }

        /*
         * search jobs by keywords
         */
        if (isset($this->value['params']['search']) && !empty($this->value['params']['search'])) {
            $search = strtolower($this->value['params']['search']);
            $expression = $queryBuilder->expr()->operator('$text', ['$search' => $search]);
            $queryBuilder->field(null)->equals($expression->getQuery());
        }

        if (isset($this->value['sort'])) {
            foreach (explode(",", $this->value['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }
        return $queryBuilder;
    }
}
