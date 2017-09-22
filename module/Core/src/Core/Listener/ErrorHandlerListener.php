<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ErrorLoggerListener.php */
namespace Core\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Router;

/**
 * Class ErrorHandlerListener
 * @package Core\Listener
 */
class ErrorHandlerListener implements ListenerAggregateInterface
{
    
    use ListenerAggregateTrait;
    
    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
    */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleError'), $priority);
    }
    
    /**
     * @param MvcEvent $event
     */
    public function handleError(MvcEvent $event)
    {
        $error = $event->getError();
        
        if (empty($error)) {
            // do nothing if there is no error in the event
            return;
        }
        
        switch ($error) {
            case Application::ERROR_ROUTER_NO_MATCH:
                // add dummy 'no-route' route to silent routeMatch errors inside the 404 page
                $noRoute = 'no-route';
                $event->getRouter()
                    ->addRoute($noRoute, Router\Http\Literal::factory(['route' => '']));
                $event->setRouteMatch((new Router\RouteMatch([]))->setMatchedRouteName($noRoute));
            break;
        }
    }
}
