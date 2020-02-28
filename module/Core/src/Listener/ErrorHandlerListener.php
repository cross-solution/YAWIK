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

use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Router;

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
