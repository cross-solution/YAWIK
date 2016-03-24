<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
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
        foreach ($hooks as $spec) {

            if (!isset($spec['event']) || !isset($spec['service'])) {
                throw new \DomainException('Hook specification must be an array with the keys "event" and "service".');
            }
            $method = isset($spec['method']) ? $spec['method'] : null;
            $priority = isset($spec['priority']) ? $spec['priority'] : 0;

            $this->setHook($spec['event'], $spec['service'], $method, $priority);
        }

        return $this;
    }

    public function setHook($event, $service, $method = null, $priority = 0)
    {
        if (is_int($method)) {
            $priority = $method;
            $method = null;
        }
        $name = sha1($event . $service . $method . $priority);

        $this->hooks[$name] = [
            'event' => $event,
            'service' => $service,
            'method' => $method,
            'priority' => $priority,
            'instance' => null,
        ];

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

        $name = substr($method, 2);
        $spec = isset($this->hooks[$name]) ? $this->hooks[$name] : false;
        if (!$spec) {
            throw new \BadMethodCallException('No deferred listener specification found.');
        }

        $service = $spec['service'];
        $method  = $spec['method'];
        $listener = $spec['instance'];

        if (!$listener) {
            $services = $this->getServiceLocator();

            if ($services->has($service)) {
                $listener = $services->get($service);

            } else {
                if (!class_exists($service, true)) {
                    throw new \UnexpectedValueException(sprintf(
                        'Cannot create deferred listener "%s", because the class does not exist.',
                        $service
                    ));
                }

                $listener = new $service();
            }

            $this->hooks[$name]['instance'] = $listener;
        }

        if ($method && method_exists($listener, $method)) {
            return call_user_func_array([ $listener, $method ], $args);
        }

        if (is_callable($listener)) {
            return call_user_func_array($listener, $args);
        }

        throw new \UnexpectedValueException(sprintf(
            'Deferred listener %s%s is not callable.',
            get_class($listener), $method ? ' has no method "' . $method . '" and ' : ''
        ));
    }
}