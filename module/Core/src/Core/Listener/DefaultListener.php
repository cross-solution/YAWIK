<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\MvcEvent;

class DefaultListener implements ListenerAggregateInterface, ServiceManagerAwareInterface
{
    protected $serviceLocator;

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceLocator = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceLocator;
    }

    public function attach(EventManagerInterface $events)
    {
        $eventsApplication = $this->getServiceManager()->get("Application")->getEventManager();

        $postDispatch = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'postDispatch'), 1000);
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
    public function postDispatch(MvcEvent $e) {
       // $view = $this->getServiceManager()->get('view');

    }

}
