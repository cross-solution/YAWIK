<?php

namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class StringListener implements ListenerAggregateInterface
{
      protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = -80)
    {
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($this, 'injectStringIntoLayout'), -80);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        /*
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
        */
    }
    
    public function injectStringIntoLayout(MvcEvent $e)
    {
        $result = $e->getResult();
        if (!is_string($result)) {
            return;
        }
        $viewModel = $e->getViewModel();

        $capture = $viewModel->captureTo();
        if (!empty($capture)) {
            if ($viewModel->isAppend()) {
                $oldResult = $viewModel->{$capture};
                $viewModel->setVariable($capture, $oldResult . $result);
            } else {
                $viewModel->setVariable($capture, $result);
            }
        }
        return;
    }
}
