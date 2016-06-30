<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Event\Listener;

use Core\Controller\Plugin\CreatePaginator;
use Core\Paginator\PaginatorService;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CreatePaginatorListener implements ListenerAggregateInterface
{
    /**
     * Attached listeners
     *
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners;

    /**
     * @var PaginatorService
     */
    protected $paginators;

    public function __construct(PaginatorService $paginators)
    {
        $this->paginators = $paginators;
    }

    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach(
            '*',
            CreatePaginator::EVENT_CREATE_PAGINATOR,
            array($this,'onCreatePaginator'),
            1
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach($this->listeners as $index=>$listener){
            if($events->detach($listener)){
                unset($this->listeners[$index]);
            }
        }
    }

    public function onCreatePaginator(EventInterface $e)
    {
        $service = false;
        $params = $e->getParams();
        $paginatorName = $params['paginatorName'];
        $paginators = $this->paginators;
        $serviceName = 'Solr/'.$paginatorName;
        if($paginators->has($serviceName)){
            // yes, we have that solr paginator to replace $paginatorName
            $service = $paginators->get($serviceName,$params['params']);
        }
        return $service;
    }

    static public function factory(ServiceLocatorInterface $sl)
    {
        return new static($sl->get('Core/PaginatorService'));
    }
}