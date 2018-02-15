<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Paginator;

use Core\Paginator\PaginatorFactoryAbstract;

/**
 * Class ListJobPaginatorFactory
 *
 * @package Organizations\Paginator
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 */
class ListJobPaginatorFactory extends PaginatorFactoryAbstract
{
    protected function getFilter()
    {
        return 'Organizations/ListJobQuery';
    }

    protected function getRepository()
    {
        return 'Jobs/Job';
    }
}
