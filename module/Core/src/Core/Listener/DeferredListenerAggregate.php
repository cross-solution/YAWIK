<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DeferredListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $hooks = [];
    protected $listeners = [];

    public function __construct($hooks = [])
    {
        $this->setHooks($hooks);
    }

    public function setHooks(array $hooks) {
        foreach ($hooks as $name => $spec) {
            if (is_numeric($name)) {
                $name = null;
            }

            if (!isset($spec['event']) || !isset($spec['service'])) {
                throw new \DomainException('Hook specification must be an array with the keys "event" and "service".');
            }
            $method = isset($spec['method']) ? $spec['method'] : null;

            $this->setHook($spec['event'], $spec['service'], $method, $name);
        }

        return $this;
    }

    public function setHook($event, $service, $method = null, $name = null)
    {
        if (!$name) { $name = sha1($event . $service . $method); }

        $spec = [ 'event' => $event, 'service' => $service ];
        if (null !== $method) { $spec['method'] = $method; }

        $this->hooks[$name] = $spec;

        return $this;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        foreach ($this->hooks as $name => $spec) {
            $listeners[] = $events->attach($spec['event'], array($this, "do$name"));
        }
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $i => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$i]);
            }
        }

        return empty($this->listeners);
    }


    public function __call($method, $args)
    {
        if (0 !== strpos($method, 'do')) {
            throw new \BadMethodCallException('Unknown method "' . $method . '"');
        }

        $event    = isset($args[0]) && $args[0] instanceOf EventInterface ? $args[0] : null;

        if (!$event) {
            throw new \InvalidArgumentException('First argument must be an EventInterface');
        }

        $name = substr($method, 2);
        $spec = isset($this->hooks[$name]) ? $this->hooks[$name] : false;
        if (!$spec) {
            throw new \BadMethodCallException('No deferred listener specification found.');
        }

        $services = $this->getServiceLocator();
        $listener = $services->get($this->hooks[$name]['service']);
        $method = isset($spec['method']) ? $spec['method'] : null;


        if ($method && method_exists($listener, $method)) {
            return $listener->{$method}($event);
        }

        if (is_callable($listener)) {
            return $listener($event);
        }

        throw new \UnexpectedValueException(sprintf(
            'Deferred listener %s%s is not callable.',
            get_class($listener), $method ? ' has no method "' . $method . '" and ' : ''
        ));
    }
}