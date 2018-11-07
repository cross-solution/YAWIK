<?php


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class AjaxRenderListener implements ListenerAggregateInterface
{

    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $sharedListeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callback = array($this, 'injectAjaxTemplate');
        /*
         * We need to hack a little, because injectViewModelListener is attached to the
         * shared event manager and triggered in Controller, we need to attach to the
         * EVENT_DISPATCH event in the shared manager, to be sure that this listener is
         * run before injectViewModelListener
         */
        $this->sharedListeners[] = $events->getSharedManager()->attach(
            'Zend\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            $callback,
            -95
        );
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, $callback, -95);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, $callback, -95);
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
        $sharedEvents = $events->getSharedManager();
        foreach ($this->sharedListeners as $index => $listener) {
            if ($sharedEvents->detach($listener)) {
                unset($this->sharedListeners[$index]);
            }
        }
    }
    
    public function injectAjaxTemplate(MvcEvent $e)
    {
        if ($e->getRequest()->isXmlHttpRequest()) {
            $viewModel = $e->getResult();
            if ($viewModel instanceof JsonModel) {
                return;
            }
            $resolver = $e->getApplication()->getServiceManager()->get('ViewResolver');

            $template = $viewModel->getTemplate() . '.ajax';
            if ($resolver->resolve($template)) {
                $viewModel->setTemplate($template);
            } else {
                /*
                 * Due to a bug in TemplatePathStackResolver we have to set the suffix here
                 * Maybe we should write a own ajax template resolver or just not use a dot here.
                 */
                $template .= '.phtml';
                if ($resolver->resolve($template)) {
                    $viewModel->setTemplate($template);
                } else {
                    $viewModel->setVariable('isAjaxRequest', true);
                }
            }
            /*
             * Disable layout. This works because InjectViewModelListener is executed after us.
             */
            $viewModel->setTerminal(true);
        }
    }
}
