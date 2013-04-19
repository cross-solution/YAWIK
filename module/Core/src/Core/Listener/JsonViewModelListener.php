<?php


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class JsonViewModelListener implements ListenerAggregateInterface
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
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), $priority);
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
    
    public function onDispatch(MvcEvent $e)
    {
        if (($viewModel = $e->getResult()) instanceOf \Zend\View\Model\JsonModel) {
            $viewModel->setTerminal(true);
            
            $strategy = new \Zend\View\Strategy\JsonStrategy(
                    new \Zend\View\Renderer\JsonRenderer
            );
            
            $view = $e->getApplication()->getServiceManager()->get('ViewManager')->getView();
            $view->addRenderingStrategy(array($strategy, 'selectRenderer'), 10);
            $view->addResponseStrategy(array($strategy,  'injectResponse'), 10);
        }        
    }
}