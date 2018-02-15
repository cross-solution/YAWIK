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
use Jobs\Entity\Status;

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
        $queryBuilder
            ->field('organization')
            ->equals($params['organization_id'])
        ;
        $queryBuilder->field('status.name')->in([
            Status::ACTIVE,
            Status::PUBLISH
        ]);
        $queryBuilder->field('isDraft')->equals(false);
        return $queryBuilder;
    }
}
