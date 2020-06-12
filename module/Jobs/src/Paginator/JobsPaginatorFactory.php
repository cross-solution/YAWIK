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
