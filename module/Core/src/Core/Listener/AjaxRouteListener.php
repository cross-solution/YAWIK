<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener;

use Core\EventManager\EventManager;
use Core\EventManager\ListenerAggregateTrait;
use Core\Listener\Events\AjaxEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Json\Json;
use Zend\Mvc\MvcEvent;

/**
 * On Route listener which triggers ajax events.
 *
 * If the query param "ajax" is present (and an ajax request is made), it will
 * trigger an AjaxEvent named after the value of the ajax param.
 *
 * The events are triggered on the dedicated event manager
 * "Core/Ajax/Events"
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class AjaxRouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * The ajax events manager.
     *
     * @var EventManager
     */
    private $ajaxEventManager;

    /**
     * Attach events spec.
     *
     * @see {@ListenerAggregateTrait}
     * @var array
     */
    private $events = [
        [ MvcEvent::EVENT_ROUTE, 'onRoute', 100 ],
    ];

    /**
     * Creates an instance.
     *
     * @param EventManager $ajaxEventManager
     */
    public function __construct(EventManagerInterface $ajaxEventManager)
    {
        $this->ajaxEventManager = $ajaxEventManager;
    }

    /**
     * Handler for onRoute mvc event.
     *
     * @param MvcEvent $event
     *
     * @return null|\Zend\Http\PhpEnvironment\Response
     * @throws \UnexpectedValueException
     */
    public function onRoute(MvcEvent $event)
    {
        /* @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $event->getRequest();
        $ajax = $request->getQuery()->get('ajax');

        if (!$request->isXmlHttpRequest() || !$ajax) {
            /* no ajax request or required parameter not present */
            return;
        }

        /* @var \Zend\Http\PhpEnvironment\Response $response */
        /* @var AjaxEvent $ajaxEvent */
        $response = $event->getResponse();
        
        $ajaxEvent = $this->ajaxEventManager->getEvent($ajax, $this);
        $ajaxEvent->setRequest($request);
        $ajaxEvent->setResponse($response);

        $results = $this->ajaxEventManager->triggerEventUntil(function($r) { return null !== $r; }, $ajaxEvent);
        $result  = $results->last() ?: $ajaxEvent->getResult();

        if (!$result) {
            throw new \UnexpectedValueException('No listener returned anything. Do not know what to do...');
        }

        /* Convert arrays or traversable objects to JSON string. */
        if (is_array($result) || $result instanceOf \Traversable) {
            $result = Json::encode($result, true, ['enableJsonExprFinder' => true]);
        }

        $contentType = $ajaxEvent->getContentType();

        $response->getHeaders()->addHeaderLine('Content-Type', $contentType);
        $response->setContent($result);

        return $response;
    }
    
}