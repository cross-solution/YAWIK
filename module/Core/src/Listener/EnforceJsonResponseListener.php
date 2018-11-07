<?php


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class EnforceJsonResponseListener implements ListenerAggregateInterface
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
    public function attach(EventManagerInterface $events, $priority = -10)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), $priority);
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
        $this->onInvokation($e);
    }
    
    public function onDispatchError(MvcEvent $e)
    {
        $this->onInvokation($e, true);
    }
    
    protected function onInvokation(MvcEvent $e, $error = false)
    {
        $viewModel = $e->getResult();
        $isJsonModel = $viewModel instanceof JsonModel;
        $routeMatch = $e->getRouteMatch();
        
        if (
            ($routeMatch && $routeMatch->getParam('forceJson', false))
            || $isJsonModel
            || "json" == $e->getRequest()->getQuery('format')
            || "json" == $e->getRequest()->getPost('format')
        ) {
            if (!$isJsonModel) {
                $model = new JsonModel();
                
                if ($error) {
                    $model->status = 'error';
                    $model->message = $viewModel->message;
                    if ($viewModel->display_exceptions) {
                        if (isset($viewModel->exception)) {
                            $model->exception = $viewModel->exception->getMessage();
                        }
                    }
                } else {
                    $model->setVariables($viewModel->getVariables());
                }
                $viewModel = $model;
                $e->setResult($model);
                $e->setViewModel($model);
            }
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
