<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Tracy\Debugger;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 * @package Core\Listener
 */
class TracyListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * True if debugging is started
     * @var bool
     */
    private $started = false;

    public function startListen()
    {
        $this->started = true;
    }

    /**
     * {@inheritDoc}
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleError'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleError'], $priority);
    }
    
    /**
     * @param MvcEvent $e
     */
    public function handleError(MvcEvent $e)
    {
        /* do not handle error if not start to listening */
        if (!$this->started) {
            return;
        }

        if ($e->getError() == \Zend\Mvc\Application::ERROR_EXCEPTION) {
            if (Debugger::$productionMode) {
                // log an exception in production environment (this will send email as well if email address is set)
                Debugger::log($e->getParam('exception'), Debugger::ERROR);
            } else {
                // just re-throw an exception in non-production environment to let tracy display so called blue screen
                throw $e->getParam('exception');
            }
        }
    }
}
