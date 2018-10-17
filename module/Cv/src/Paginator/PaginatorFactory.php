<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 20/06/16
 * Time: 14:59
 */

namespace Cv\Paginator;

use Core\Paginator\PaginatorFactoryAbstract;

class PaginatorFactory extends PaginatorFactoryAbstract
{
    protected function getFilter()
    {
        return 'Cv/PaginationQuery';
    }

    protected function getRepository()
    {
        return 'Cv/Cv';
    }
}
