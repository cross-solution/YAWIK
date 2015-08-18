<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
     * @return \Core\Paginator\Zend\Filter\FilterInterface|string
     */
    protected function getFilter()
    {
        return 'Jobs/PaginationAdminQuery';
    }

    /**
     * @return \Core\Paginator\Zend\Filter\FilterInterface\Repository|string
     */
    protected function getRepository()
    {
        return 'Jobs/Job';
    }
}
