<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\MvcEvent;
use Core\Listener\InjectNotificationsViewModelListener;

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
        //$events->attach(new InjectNotificationsViewModelListener());
        $eventsApplication->attach(new InjectNotificationsViewModelListener());

        return $this;
    }

    public function detach(EventManagerInterface $events)
    {
        return $this;
    }
}
