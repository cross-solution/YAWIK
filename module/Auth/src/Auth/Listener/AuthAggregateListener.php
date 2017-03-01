<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Listener;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceManager;
use Auth\Listener\Events\AuthEvent;

class AuthAggregateListener
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $mailForgotPassword = $this->serviceManager->get('Auth/Listener/MailForgotPassword');
        $events->attach('Auth', AuthEvent::EVENT_AUTH_NEWPASSWORD, $mailForgotPassword, 10);
        return $this;
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        return $this;
    }
    
    /**
     * @param ServiceManager $serviceLocator
     * @return AuthAggregateListener
     */
    public static function factory(ServiceManager $serviceLocator)
    {
        return new static($serviceLocator);
    }
}
