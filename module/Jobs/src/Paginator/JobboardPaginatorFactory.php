<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Jobs\Paginator;

use Core\Paginator\PaginatorFactoryAbstract;
use Jobs\Repository\Filter\JobboardPaginationQuery;

/**
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class JobboardPaginatorFactory extends PaginatorFactoryAbstract
{
    /**
     * @return string
     */
    protected function getFilter()
    {
        return JobboardPaginationQuery::class;
    }

    /**
     * @return string
     */
    protected function getRepository()
    {
        return 'Jobs/Job';
    }
}
