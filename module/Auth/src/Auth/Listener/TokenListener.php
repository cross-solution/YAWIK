<?php


namespace Auth\Listener;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\Mvc\MvcEvent;


class TokenListener implements SharedListenerAggregateInterface
{

    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

   
    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attachShared(SharedEventManagerInterface $events, $priority = 1000)
    {
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'), $priority);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onBootstrap(MvcEvent $e)
    {
        $request = $e->getRequest();
        $token = $request->getPost('auth');
        
        if (!$token) {
            $token = $request->getQuery('auth');
        }
        if (!$token) {
            return;
        }
        
        session_id($token);
        
    }
}
