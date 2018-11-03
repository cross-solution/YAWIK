<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;

class DefaultListener implements ListenerAggregateInterface
{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function attach(EventManagerInterface $events, $priority=1000)
    {
        $eventsApplication = $this->serviceLocator->get("Application")->getEventManager();

        $postDispatch = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'postDispatch'), $priority);
        //$events->detach($postDispatch);

        return $this;
    }

    public function detach(EventManagerInterface $events)
    {
        return $this;
    }

    /**
     * @todo why this was added?
     *
     * this breaks
     *
     * YAWIK/bin$ ./console applications cleanup
     */
    public function postDispatch(MvcEvent $e)
    {
        // $view = $this->serviceLocator->get('view');
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return DefaultListener
     */
    public static function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator);
    }
}
