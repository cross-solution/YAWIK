<?php

namespace Acl\Service;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class Acl implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 100);
    }
    
    public function detach(EventManagerInterface $events) {
    }
    
    public function onDispatch(MvcEvent $e) {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof Router\RouteMatch) {
            // Can't do anything without a route match
            return;
        }
    }
}