<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Paginator;

use Core\Paginator\PaginatorFactoryAbstract;

/**
 * Class JobsAdminPaginatorFactory
 * @package Jobs\Paginator
 */
class JobsAdminPaginatorFactory extends PaginatorFactoryAbstract
{

    /**
     * @return string
     */
    protected function getFilter()
    {
        return 'Jobs/PaginationAdminQuery';
    }

    /**
     * @return string
     */
    protected function getRepository()
    {
        return 'Jobs/Job';
    }
}
