<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Listener;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Auth\Listener\Events\AuthEvent;

class AuthAggregateListener implements SharedListenerAggregateInterface, ServiceManagerAwareInterface
{

    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $mailForgotPassword = $this->getServiceManager()->get('Auth/Listener/MailForgotPassword');
        $events->attach('Auth', AuthEvent::EVENT_AUTH_NEWPASSWORD, $mailForgotPassword, 10);
        return $this;
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        return $this;
    }
}
