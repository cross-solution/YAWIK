<?php


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

/**
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 * @since 0.30 - do nothing if no route matched.
 */
class XmlRenderListener implements ListenerAggregateInterface
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
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callback = array($this, 'injectXmlTemplate');
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
            -96
        );
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, $callback, -96);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, $callback, -96);
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
        /* @var SharedEventManager $sharedEvents */
        $sharedEvents = $events->getSharedManager();
        foreach ($this->sharedListeners as $index => $listener) {
            if ($sharedEvents->detach($listener)) {
                unset($this->sharedListeners[$index]);
            }
        }
    }

    /**
     *
     *
     * @param MvcEvent $e
     *
     * @since 0.30 - Return early if no $routeMatch is set.
     */
    public function injectXmlTemplate(MvcEvent $e)
    {
        if (!($routeMatch = $e->getRouteMatch())) {
            return;
        };

        $format = $e->getRouteMatch()->getParam('format', "html");
        $channel = $e->getRouteMatch()->getParam('channel', "default");

        if ('xml' == $format) {
            $viewModel = $e->getResult();
            if ($viewModel instanceof JsonModel) {
                return;
            }
            $resolver = $e->getApplication()->getServiceManager()->get('ViewResolver');

            $templateDefault = $viewModel->getTemplate() . '.xml.phtml';
            $templateChannel = $viewModel->getTemplate() . '.' . $channel . '.xml.phtml';


            if ($channel != 'default' && $resolver->resolve($templateChannel)) {
                $viewModel->setTemplate($templateChannel);
            } elseif ($resolver->resolve($templateDefault)) {
                $viewModel->setTemplate($templateDefault);
            } else {
            }
            /*
             * Disable layout. This works because InjectViewModelListener is executed after us.
             */
            $viewModel->setTerminal(true);

            /* @var Response $response */
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/xml');
        }
    }
}
