<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MongoPersistenceListener.php */ 
namespace Core\Repository\DoctrineMongoODM;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class PersistenceListener implements ListenerAggregateInterface
{

    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    protected $hasRun = false;

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'flushDocumentManager'), -150);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 10);
        /* This is needed to handle cases where the DISPATCH event exists early due to shortCircuit check */
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH,   array($this, 'flushDocumentManager'), 150);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    } 
    
    public function onDispatchError(MvcEvent $event)
    {
        /* Disable execution on EVENT_FINISH and EVENT_DISPATCH */
        $this->hasRun = true;
    }
    
    public function flushDocumentManager(MvcEvent $event)
    {
        if ($this->hasRun) {
            return;
        }
        
        $services = $event->getApplication()->getServiceManager();
        $dm       = $services->get('Core\DocumentManager');

        $dm  ->flush();
        $this->hasRun = true;
    }
}

