<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;

/**
 * maps query parameters to entity attributes
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 *
 */
class PaginationSearchUsers extends AbstractPaginationQuery
{

    protected $sortPropertiesMap = [
        'name' => [ 'info.lastName', 'info.firstName', 'info.email' ],
    ];

    /**
     * @param $params
     * @param \Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     *
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        if (!empty($params['text'])) {
            $queryBuilder->text($params['text']);
        }
        $queryBuilder->field('isDraft')->equals(false);

        if (isset($params['sort'])) {
            foreach (explode(",", $params['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }

        return $queryBuilder;
    }
}
