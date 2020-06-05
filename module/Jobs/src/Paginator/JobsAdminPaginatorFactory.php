<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
