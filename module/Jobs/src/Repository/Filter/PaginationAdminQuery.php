<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Jobs\Entity\Status;
use \Doctrine\ODM\MongoDB\Query\Builder;
use MongoDB\BSON\ObjectId;

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
            $this->filterTextSearch($queryBuilder, $params['text']);
        }

        $queryBuilder->field('isDraft')->equals(false);

        if (isset($params['status']) &&
            !empty($params['status'])) {
            if ($params['status'] != 'all') {
                $queryBuilder->field('status.name')->equals($params['status']);
            }
        }

        if (isset($params['companyId']) &&
            !empty($params['companyId'])) {
            $queryBuilder->field('organization')->equals(new ObjectId($params['companyId']));
        }

        if (isset($params['sort'])) {
            foreach (explode(",", $params['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }
        $queryBuilder->sort('datePublishStart.date', -1);
        return $queryBuilder;
    }

    private function filterTextSearch($qb, $text)
    {
        $jobIds = [];

        while (preg_match("~job:([^\s]+)~s", $text, $matches)) {
            $jobIds = array_merge($jobIds, explode(',', $matches[1]));
            $text = str_replace($matches[0], '', $text);
        }

        if (count($jobIds)) {
            $qb->field('id')->in($jobIds);
        }

        $search = trim(strtolower($text));
        $search && $qb->text($search);
    }
}
