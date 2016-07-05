<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Paginator;
use Solr\Options\ModuleOptions;

/**
 * Provide Solr version of JobsPaginatorFactory
 * This paginator factory will replace Jobs/Board with Solr/Jobs/Board paginator
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @see     Jobs\Paginator\JobPaginatorFactory
 * @package Solr\Paginator
 */
class JobsBoardPaginatorFactory extends PaginatorFactoryAbstract
{
    protected function getFilter()
    {
        return 'Solr/Jobs/PaginationQuery';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnectPath(ModuleOptions $options)
    {
        return $options->getJobsPath();
    }
}