<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Doctrine\ODM\MongoDB\Query\Builder;
use Interop\Container\ContainerInterface;
use Jobs\Entity\Job;

/**
 * Class ListJobQuery
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Organizations\Repository\Filter
 * @since 0.30
 */
class ListJobQuery extends AbstractPaginationQuery
{
    protected $repositoryName = 'Jobs/Job';

    /**
     * @param   array   $params
     * @param   Builder $queryBuilder
     *
     * @return Builder|mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        $id = $params['organization_id'];
        $queryBuilder
            ->field('organization')
            ->equals($id)
        ;
        $queryBuilder->field('status.name')->equals('active');
        $queryBuilder->field('isDraft')->equals(false);
        return $queryBuilder;
    }
}
