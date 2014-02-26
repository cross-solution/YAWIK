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
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = -100)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRender'), $priority);
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
    
    public function onRender(MvcEvent $e)
    {
        if ($e->getRequest()->isXmlHttpRequest()) {
            $viewModel = $e->getResult();
            if ($viewModel instanceOf JsonModel) {
                return;
            }
            $resolver = $e->getApplication()->getServiceManager()->get('ViewResolver');
            /*
             * Due to a bug in TemplatePathStackResolver we have to set the suffix here
             * Maybe we should write a own ajax template resolver or just not use a dot here.
             */
            $template = $viewModel->getTemplate() . '.ajax.phtml';
            if ($resolver->resolve($template)) {
                $viewModel->setTemplate($template);
            } else {
                $viewModel->setVariable('isAjaxRequest', true);
            } 
            //$viewModel->setTerminal(true);
            $e->setViewModel($viewModel);
        }
                
    }
}