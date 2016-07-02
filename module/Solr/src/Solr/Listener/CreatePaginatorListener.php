<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Listener;

use Core\Listener\Events\CreatePaginatorEvent;

/**
 * Class CreatePaginatorListener
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Solr\Event\Listener
 */
class CreatePaginatorListener
{
    /**
     * Replace paginator like Jobs/Board with Solr/Jobs/Board
     *
     * @param CreatePaginatorEvent $event
     */
    public function onCreatePaginator(CreatePaginatorEvent $event)
    {
        $params = $event->getPaginatorParams();
        $paginatorName = $event->getPaginatorName();
        $paginators = $event->getPaginators();
        $serviceName = 'Solr/'.$paginatorName;
        if($paginators->has($serviceName)){
            /* @var \Zend\Paginator\Paginator $paginator */
            // yes, we have that solr paginator to replace $paginatorName
            $paginator = $paginators->get($serviceName,$params);
            $event->setPaginator($paginator);
        }
    }

    /**
     * Create and return new instance for CreatePaginatorListener
     *
     * @return CreatePaginatorListener
     */
    static public function factory()
    {
        return new self();
    }
}