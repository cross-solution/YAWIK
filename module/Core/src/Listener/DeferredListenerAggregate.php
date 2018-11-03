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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Lazy loads listeners.
 *
 * Registered listeners will be created and/or invoked by this aggregate,
 * if and only if the associated event is triggered from the event manager
 * this aggregate is attached to.
 *
 * If you do not retrieve an instance through the service manager you have to inject
 * the service manager instance yourself.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class DeferredListenerAggregate implements ListenerAggregateInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    
    /**
     * Registered listener specifications.
     *
     * @var array
     */
    protected $listenerSpecs = [];

    /**
     * Listener handles from the event manager,
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * Creates an instance.
     *
     * Calls {@link setListeners()}.
     *
     * @param ServiceLocatorInterface $serviceManager
     * @param array $specs
     */
    public function __construct(ServiceLocatorInterface $serviceManager, $specs = [])
    {
        $this->serviceManager = $serviceManager;
        $this->setListeners($specs);
    }

    /**
     * Alias for setListeners().
     *
     * @param array $hooks
     *
     * @codeCoverageIgnore
     * @deprecated since 0.25 use setListeners() instead.
     * @see setListeners
     * @return self
     */
    public function setHooks(array $hooks)
    {
        return $this->setListeners($hooks);
    }

    /**
     * Alias for setListener().
     *
     * @param      $event
     * @param      $service
     * @param null $method
     * @param int  $priority
     *
     * @codeCoverageIgnore
     * @deprecated since 0.25 use setListener() instead.
     * @see setListener
     * @return self
     */
    public function setHook($event, $service, $method = null, $priority = 0)
    {
        return $this->setListener($event, $service, $method, $priority);
    }

    /**
     * Adds multiple listener specifications.
     *
     * <b>$specs</b> must be an array of arrays in the following format:
     * <pre>
     * [
     *      [
     *          'event' => <eventName>,
     *          'service' => <serviceNameOrFQCN>,
     *          { 'method' => <callbackMethodName>, }
     *          { 'priority' => <priority> }
     *      ],
     *      ...
     * ]
     * </pre>
     *
     * @param array $specs
     *
     * @see setListener
     * @return self
     * @throws \DomainException if a specification array does not contain the keys 'event' or 'service'.
     * @since 0.25
     */
    public function setListeners(array $specs)
    {
        foreach ($specs as $spec) {
            if (!isset($spec['event']) || !isset($spec['service'])) {
                throw new \DomainException('Listener specification must be an array with the keys "event" and "service".');
            }
            $method = isset($spec['method']) ? $spec['method'] : null;
            $priority = isset($spec['priority']) ? $spec['priority'] : 0;

            $this->setListener($spec['event'], $spec['service'], $method, $priority);
        }

        return $this;
    }

    /**
     * Adds a listener specification.
     *
     * @param string     $event
     * @param string     $service
     * @param null|string|int $method
     * @param int  $priority
     *
     * @return self
     * @since 0.25
     */
    public function setListener($event, $service, $method = null, $priority = 0)
    {
        if (is_int($method)) {
            $priority = $method;
            $method = null;
        }

        $name = uniqid();

        $this->listenerSpecs[$name] = [
            'event' => $event,
            'service' => $service,
            'method' => $method,
            'priority' => $priority,
            'instance' => null,
        ];

        return $this;
    }

    /**
     * Attachs listener creation and invokation callback.
     *
     * For each specification this aggregate attachs itself to the event manager on the
     * specified event to be able to react upon triggering.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        foreach ($this->listenerSpecs as $name => $spec) {
            $this->listeners[] = $events->attach($spec['event'], array($this, "do$name"), $spec['priority']);
        }
    }

    /**
     * Detach all listener creation and invokation callbacks.
     *
     * @param EventManagerInterface $events
     *
     * @return boolean
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

    /**
     * Callback for creation and invokation of listeners.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed The return value of the listener invokation.
     *
     * @throws \UnexpectedValueException if listener specification could not be found or listener could not be created.
     * @throws \BadMethodCallException if the method called does not start with "do".
     */
    public function __call($method, $args)
    {
        if (0 !== strpos($method, 'do')) {
            throw new \BadMethodCallException('Unknown method "' . $method . '"');
        }

        $name = substr($method, 2);
        $spec = isset($this->listenerSpecs[$name]) ? $this->listenerSpecs[$name] : false;

        if (!$spec) {
            throw new \UnexpectedValueException('No deferred listener specification found.');
        }

        $service = $spec['service'];
        $method  = $spec['method'];
        $listener = $spec['instance'];

        if (!$listener) {
            if ($this->serviceManager->has($service)) {
                $listener = $this->serviceManager->get($service);
            } else {
                if (!class_exists($service, true)) {
                    throw new \UnexpectedValueException(sprintf(
                        'Cannot create deferred listener "%s", because the class does not exist.',
                        $service
                    ));
                }

                $listener = new $service();
            }

            $this->listenerSpecs[$name]['instance'] = $listener;
        }

        if ($method && method_exists($listener, $method)) {
            return call_user_func_array([ $listener, $method ], $args);
        }

        if (is_callable($listener)) {
            return call_user_func_array($listener, $args);
        }

        throw new \UnexpectedValueException(sprintf(
            'Deferred listener %s%s is not callable.',
            get_class($listener),
            $method ? ' has no method "' . $method . '" and ' : ''
        ));
    }
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return DeferredListenerAggregate
     */
    public static function factory(ServiceLocatorInterface $serviceManager)
    {
        return new static($serviceManager);
    }
}
