<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Orders\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class PaginationQuery extends AbstractPaginationQuery
{
    protected $sortPropertiesMap = [
        'date' => 'dateCreated.date',
    ];

    /**
     * @param $params
     * @param $queryBuilder
     *
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        if (!empty($params['text'])) {
            $queryBuilder->text($params['text']);
        }

        $sort = $this->filterSort($params['sort']);
        $queryBuilder->sort($sort);

        return $queryBuilder;
    }


}