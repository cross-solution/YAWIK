<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** CheckRouteListener.php */
namespace Acl\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Auth\Exception\UnauthorizedAccessException;
use Zend\Permissions\Acl\AclInterface;
use Auth\Entity\UserInterface;

class CheckPermissionsListener implements ListenerAggregateInterface
{

    protected $user;
    protected $acl;
    protected $exceptionMap = array();

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    public function __construct(AclInterface $acl, UserInterface $user, $exceptionMap = array())
    {
        $this->acl = $acl;
        $this->user = $user;
        $this->exceptionMap = $exceptionMap;
    }
    
    public function getExceptionClass($resourceId)
    {
        return isset($this->exceptionMap[$resourceId])
            ? $this->exceptionMap[$resourceId]
            : '\Auth\Exception\UnauthorizedAccessException';
    }
    
    public function getAcl()
    {
        return $this->acl;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
    */
    public function attach(EventManagerInterface $events, $priority=1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -10);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 10);
    }
    
    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
	
	/**
	 * @param MvcEvent $event
	 *
	 * @return array|\ArrayAccess|mixed|object
	 */
    public function onRoute(MvcEvent $event)
    {
        if ($event->isError()) {
            return $event->getResult();
        }
        $routeMatch = $event->getRouteMatch();
        $routeName  = $routeMatch->getMatchedRouteName();
        $resourceId = "route/$routeName";
        
        return $this->checkAcl($event, $resourceId);
    }
    
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();
        $controller = ltrim($routeMatch->getParam('controller'), '\\');
        $action     = $routeMatch->getParam('action');

        return $this->checkAcl($event, $controller, $action);
    }
    
    protected function checkAcl(MvcEvent $event, $resourceId, $privilege = null)
    {
        $role = $this->getUser();
        $acl  = $this->getAcl();
        
        $resourceId = $this->findResourceId($resourceId);


        if ($resourceId && !$acl->isAllowed($role, $resourceId, $privilege)) {
           /*
            * Exceptions are only catched within the dispatch listener, so
            * we have to set the exception manually in the event
            * and trigger the DISPATCH_ERROR event.
            */
            $exceptionClass = $this->getExceptionClass($resourceId);
            $event->setError('Access denied');
            $event->setParam('exception', new $exceptionClass('You are not permitted to view this resource'));
            $eventManager = $event->getTarget()->getEventManager();
            $eventManager->setEventPrototype($event);
            $results = $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $event->getParams();
            }
            return $return;
        }
        
        return $event->getResult();
    }

    protected function findResourceId($resourceId)
    {
        $acl = $this->getAcl();

        if ($acl->hasResource($resourceId)) {
            return $resourceId;
        }

        $parts = explode('/', $resourceId);

        if (1 == count($parts)) {
            return false;
        }

        array_pop($parts);
        $resourceId = join('/', $parts);

        return $this->findResourceId($resourceId);
    }
}
