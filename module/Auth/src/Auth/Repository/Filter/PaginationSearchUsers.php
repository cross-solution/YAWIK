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
    /**
     * @param $params
     * @param \Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     *
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        $this->value = $params;
        $queryBuilder->field('isDraft')->equals(false);
        return $queryBuilder;
    }

}
