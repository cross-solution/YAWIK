<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Paginator;


use Solr\Paginator\PaginatorFactoryAbstract;

class JobsBoardPaginatorFactory extends PaginatorFactoryAbstract
{
    protected function getFilter()
    {
        return 'Solr/Jobs/PaginationQuery';
    }

    protected function getConnectPath()
    {
        return '/solr/YawikJobs';
    }
}