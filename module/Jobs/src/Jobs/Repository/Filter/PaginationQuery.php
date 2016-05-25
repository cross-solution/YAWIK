<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Jobs\Entity\Status;
use Auth\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zend\Stdlib\Parameters;
use Auth\Entity\UserInterface;

/**
 * maps query parameters to entity attributes
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 *
 */
class PaginationQuery extends AbstractPaginationQuery
{

    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var array
     */
    protected $value;

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
     * for people
     * following parameter are relevant
     * by     => 'all', 'me', 'guest'
     * status => Status::CREATED, 'all'
     * user   => User::ROLE_RECRUITER, User::ROLE_ADMIN, User::ROLE_USER
     *
     * @param $params Parameters
     * @param $queryBuilder \Doctrine\ODM\MongoDB\Query\Builder
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        $this->value = $params;

        /*
         * search jobs by keywords
         */
        if (isset($params['search']) && !empty($params['search'])) {
            $search = strtolower($params['search']);
            $expression = $queryBuilder->expr()->operator('$text', ['$search' => $search]);
            $queryBuilder->field(null)->equals($expression->getQuery());
        }

        if (isset($this->value['location']->coordinates)) {
            $coordinates = $this->value['location']->coordinates->getCoordinates();
            $queryBuilder->field('locations.coordinates')->geoWithinCenter($coordinates[0], $coordinates[1], (float) $this->value['d']/100);
        }


        $this->user = $this->auth->getUser();
        $isRecruiter = $this->user->getRole() == User::ROLE_RECRUITER || $this->acl->inheritsRole($this->user, User::ROLE_RECRUITER);

        if ($isRecruiter && (!isset($this->value['by']) || $this->value['by'] != 'guest')) {
            /*
             * a recruiter can see his jobs and jobs from users who gave permissions to do so
             */
            if (isset($params['by']) && 'me' == $params['by']) {
                $queryBuilder->field('user')->equals($this->user->id);
            } else {
                $queryBuilder->field('permissions.view')->equals($this->user->id);
            }
            if (
                isset($params['status']) &&
                !empty($params['status']) &&
                $params['status'] != 'all'
            ) {
                $queryBuilder->field('status.name')->equals((string) $params['status']);
            }
        } else {
            /*
             * an applicants or guests can see all active jobs
             */
            $queryBuilder->field('status.name')->equals(Status::ACTIVE);
        }

        if (isset($this->value['sort'])) {
            foreach (explode(",", $this->value['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }

        return $queryBuilder;
    }

    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        switch ($sortProp) {
            case "date":
                $sortProp = "datePublishStart.date";
                break;
            case "title":
                $sortProp = "title";
                break;
            case "cam":
                $sortProp = "atsEnabled";
                break;

            default:
                break;
        }
        return array($sortProp => $sortDir);
    }
}
