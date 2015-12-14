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
use Auth\Entity\User;

/**
 * Class PaginationAdminQuery
 *
 * This is currently only used to list pending jobs for approval
 *
 * @package Jobs\Repository\Filter
 */
class PaginationAdminQuery extends PaginationQuery
{

    public function createQuery($params, $queryBuilder)
    {
        $this->value = $params->toArray();
        $this->user = $this->auth->getUser();
        $queryBuilder->field('status.name')->equals(Status::CREATED);

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
