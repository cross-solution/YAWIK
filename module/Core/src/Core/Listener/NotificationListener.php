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
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\SharedEventManagerInterface;
use Core\Listener\Events\NotificationEvent;

/**
 */
class NotificationListener implements SharedListenerAggregateInterface, ServiceManagerAwareInterface
{

    protected $serviceManager;
    protected $notifications = array();

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('*', NotificationEvent::EVENT_NOTIFICATION_ADD, array($this,'add') , 1);
        return $this;
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        return $this;
    }

    public function add(NotificationEvent $event) {
        $message = $event->getMessage();
        $this->notifications[] = array('notification' => $message);
        return $this;
    }

    //public function
}