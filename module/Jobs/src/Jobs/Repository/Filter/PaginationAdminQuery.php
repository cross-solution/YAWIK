<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Jobs\Entity\Status;
use \Doctrine\ODM\MongoDB\Query\Builder;


/**
 * Class PaginationAdminQuery
 *
 * This is currently only used to list pending jobs for approval
 *
 * @package Jobs\Repository\Filter
 */
class PaginationAdminQuery extends AbstractPaginationQuery
{

    /**
     * @param array $params
     * @param Builder $queryBuilder
     *
     * @return Builder
     */
    public function createQuery($params, $queryBuilder)
    {

        /*
         * search jobs by keywords
         */

        if (isset($params['text']) && !empty($params['text'])) {
            $search = strtolower($params['text']);
            $expression = $queryBuilder->expr()->operator('$text', ['$search' => $search]);
            $queryBuilder->field(null)->equals($expression->getQuery());
        }

        $queryBuilder->field('isDraft')->equals(false);

        if (isset($params['statusselect']) &&
            !empty($params['statusselect'])) {
            if ($params['statusselect'] != 'all') {
                $queryBuilder->field('status.name')->equals($params['statusselect']);
            }
        }

        if (isset($params['companyId']) &&
            !empty($params['companyId'])) {
            $queryBuilder->field('organization')->equals(new \MongoId($params['companyId']));
        }

        if (isset($params['sort'])) {
            foreach (explode(",", $params['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }
        return $queryBuilder;
    }
}
