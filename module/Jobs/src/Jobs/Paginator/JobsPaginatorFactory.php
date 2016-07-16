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
 * Class JobsPaginatorFactory
 * @package Jobs\Paginator
 */
class JobsPaginatorFactory extends PaginatorFactoryAbstract
{

    /**
     * @return string
     */
    protected function getFilter()
    {
        return 'Jobs/PaginationQuery';
    }

    /**
     * @return string
     */
    protected function getRepository()
    {
        return 'Jobs/Job';
    }
}
