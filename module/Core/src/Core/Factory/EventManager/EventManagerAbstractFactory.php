<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\EventManager;

use Core\EventManager\EventProviderInterface;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Creates event manager instances.
 *
 * Optionally configures these instances with options set in array with the key "event_manager" in the main config
 * array. Also creates listeners which are preconfigured in the options and automatically attaches them.
 *
 * The options array:
 * [
 *      'event_manager' => [
 *          'Meaningful/Service.Name/Events' => [
 *              'service' => string: Service name or class name of the event manager to create.
 *              'event'   => string: Service name or class name of the event class to be used.
 *              'configure' => bool: Wether or not to configure the service manager through THIS factory.
 *              'identifiers' => array: list of identifiers for the event manager.
 *              'listeners' => array: preconfigured listeners which will ONLY be created, when the
 *                                    event manager is created. (lazy loading)
 *      ]
 * ]
 *
 * The listeners array:
 *
 * [
 *      string:listener => string:event, // creates the listener with the key as name (or class) and attaches it
 *                                       // to the event manager on the provided event.
 *
 *      // If you need more options or need to attach to multiple events, you can use following syntax: //
 *      string:listener => [ string|array:event{, string:methodName}{, int:priority}{, bool:lazy }],
 *                      // First string item or any array item is used as event(s)
 *                      // Any string item (if event is already set) and any following string items set (and override previous) method names
 *                      // (the method name is the name of the method to be called upon the listener when the event happens.)
 *                      // Any int item set and override previously set priority.
 *                      // Any boolean item set and override previously set lazy option.
 *                      // (lazy option does provide even more lazy loading. The listener is only created, if
 *                      //  the event it listens to is actually triggered. (accomplished by \Core\Listener\DeferredListenerAggregate)
 *
 *      string:aggregate, // Creates an ListenerAggregate and call its attach method with the instance of the event manager
 *      string:aggregate => int:priority // Same as above, but passes the priority value along.
 * ]
 *
 * If you need to attach more than one method or two events with different priorities you can do so using the
 * verbose style in the listener array:
 *
 * [
 *      string:listener => [
 *          'events' => [
 *              string:eventName,
 *              string:eventName => int:priority,
 *              string:eventName' => string:method,
 *              string:eventName => [
 *                  'method' => string:method,
 *                  'method' => [ string:method, string:method, ... ],
 *                  'method' => [ string:method => int:priority, string:method' => int:priority ],
 *                  'priority' => int:priority,
 *              ],
 *          ],
 *          'method' => string:defaultMethod,
 *          'priority' => int:defaultPriority,
 *          'lazy' => bool
 *      ],
 * ]
 *
 * Example:
 * Attach one listener to two methods on one event:
 * [
 *      'Listener' => [ 'events' => [ 'eventName' => [ 'method' => ['method1', 'method2'] ] ] ],
 * ]
 *
 * Use default priority on method1 and different priority on method2:
 * [
 *      'Listener' => [ 'events' => [ 'eventName' => [ 'method' => ['method1', 'method2' => 12] ] ] ], 'priority' => 5 ],
 * ]
 *
 * Attach one listener to two events with different priority:
 * [
 *      'Listener' => [ 'events' => [ 'event1' => 1, 'event2' => 2 ] ],
 * ]
 *
 * Each specific event -> method -> priority triple will be attached searately to the event manager using the
 * same listener instance.
 *
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class EventManagerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container, $requestedName);
        $events = $this->createEventManager($container, $config);

        $this->attachListeners($container, $events, $config['listeners']);
        return $events;
    }

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        /* We check, if $requestedName ends with the string '/Events'.
         * Instead of parsing the string with regular expressions (eg. ~/Events$~),
         * it's more efficient to just check with strpos, if the reversed string starts
         * with the reverted '/Events' string.
         */
        return 0 === strpos(strrev($requestedName), 'stnevE/');
    }
	
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * Creates an event manager and attaches preconfigured listeners.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @return \Zend\EventManager\EventManagerInterface
     * @throws \UnexpectedValueException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator,$requestedName);
    }

    /**
     * Gets configuration for an event manager.
     *
     * Merges the default config with configuration from the main config,
     * if a key $name exists in the array under the "event_manager" array in the main config.
     *
     * @param ServiceLocatorInterface $services
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($services, $name)
    {
        $defaults = [
            'service' => 'EventManager',
            'configure' => true,
            'identifiers' => [ $name ],
            'event' => '\Zend\EventManager\Event',
            'listeners' => [],
        ];

        $config = $services->get('Config');
        $config = isset($config['event_manager'][$name]) ? $config['event_manager'][$name] : [];

        /*
         * array_merge does not work, because the default values for 'identifiers' and 'listeners'
         * are arrays and array_merge breaks the structure.
         */
        $config = array_replace_recursive($defaults, $config);

        return $config;
    }

    /**
     * Creates an event manager instance.
     *
     * Fetches from the service manager or tries to instantiate direct, if no service
     * exists in the service manager.
     *
     * If the key 'configure' in the config array has the value TRUE (default),
     * the event manager instance will get configured. Which means, the event prototype
     * will be set (after it is fetched from the service manager or instatiated),
     * and the shared event manager will be injected.
     *
     * @param ServiceLocatorInterface $services
     * @param array $config
     *
     * @return \Zend\EventManager\EventManagerInterface
     * @throws \UnexpectedValueException if neither a service exists, nor could a class be found.
     */
    protected function createEventManager($services, $config)
    {
        /* @var \Zend\EventManager\EventManagerInterface|\Core\EventManager\EventProviderInterface $events */

        if ($services->has($config['service'])) {
            $events = $services->get($config['service']);

        } else {
            if (!class_exists($config['service'], true)) {
                throw new \UnexpectedValueException(sprintf(
                    'Class or service %s does not exists. Cannot create event manager instance.', $config['service']
                ));
            }

            $events = new $config['service']();
        }

        if (false === $config['configure']) {
            return $events;
        }

        $events->setIdentifiers($config['identifiers']);
	
	    /* @var \Zend\EventManager\EventInterface $event */
	    $event = $services->has($config['event']) ? $services->get($config['event']) : new $config['event']();
	    $events->setEventPrototype($event);

        if ('EventManager' != $config['service'] && method_exists($events, 'setSharedManager') && $services->has('SharedEventManager')) {
            /* @var \Zend\EventManager\SharedEventManagerInterface $sharedEvents */
            $sharedEvents = $services->get('SharedEventManager');
            $events->setSharedManager($sharedEvents);
        }

        return $events;
    }

    /**
     * Attaches listeners provided in the config to the event manager instance.
     *
     * @param ServiceLocatorInterface $services
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     * @param array $listeners
     *
     * @throws \UnexpectedValueException if a listener name cannot be fetched as service or be instantiated.
     */
    protected function attachListeners($services, $eventManager, $listeners)
    {
        $lazyListeners = [];

        foreach ($listeners as $name => $options) {
            $options = $this->normalizeListenerOptions($name, $options);

            if ($options['lazy'] && null !== $options['attach'] ) {
                foreach ($options['attach'] as $spec) {
                    $lazyListeners[] = [
                        'service' => $options['service'],
                        'event' => $spec['event'],
                        'method' => $spec['method'],
                        'priority' => $spec['priority'],
                    ];
                }
                continue;
            }

            if ($services->has($options['service'])) {
                $listener = $services->get($options['service']);

            } else if (class_exists($options['service'], true)) {
                $listener = new $options['service']();

            } else {
                throw new \UnexpectedValueException(sprintf(
                                                        'Class or service %s does not exists. Cannot create listener instance.', $options['service']
                                                    ));
            }

            if ($listener instanceOf ListenerAggregateInterface) {
                $listener->attach($eventManager, $options['priority']);
                continue;
            }

            foreach ($options['attach'] as $spec) {
                $callback = $spec['method'] ? [ $listener, $spec['method'] ] : $listener;
                $eventManager->attach($spec['event'], $callback, $spec['priority']);
            }
        }

        if (!empty($lazyListeners)) {
            /* @var \Core\Listener\DeferredListenerAggregate $aggregate */
            $aggregate = $services->get('Core/Listener/DeferredListenerAggregate');
            $aggregate->setListeners($lazyListeners)
                      ->attach($eventManager);
        }
    }

    /**
     * Normalizes the listener configuration.
     *
     * Converts the options given in the main config file to an array
     * containing key => value pairs for easier consumption in
     * {@link attachListeners()}
     *
     * @param int|string $name Service or class name of the listener. (if int, we have config for an aggregate)
     * @param string|array $options String is either event name or aggregate name (when name is int).
     *                              Array are the options from config. [ [event,..], method, priority, lazy]
     *
     * @return array
     */
    protected function normalizeListenerOptions($name, $options)
    {

        /*
         * $options is an array with following meta-syntax:
         *
         *  $options = [
         *      string:listener => string:event,
         *      string:listener => [ string|array:event{, string:methodName}{, int:priority}{, bool:lazy }],
         *      string:aggregate, // implies integer value as $name
         *      string:aggregate => int:priority,
         *      string:listener => [
         *          'events' => [ 'event', 'event' => priority, 'event' => 'method',
         *                        'event' => [ 'method' => method, 'priority' => priority ],
         *                        'event' => [ 'method' => [ 'method', 'method' => priority ], 'priority' => priority ]
         *                     ],
         *          'method' => method,
         *          'priority' => priority,
         *          'lazy' => bool
         * ]
         */

        $normalized = [
            'service' => $name,
            'attach' => null,
            'priority' => 1,
            'lazy' => false,
        ];

        if (is_int($name)) {
            /* $options must be the name of an aggregate service or class. */
            $normalized['service'] = $options;
            return $normalized;

        }

        if (is_int($options)) {
            /* $name must be the name of an aggregate and the priority is passed. */
            $normalized['priority'] = $options;
            return $normalized;

        }

        if (is_string($options)) {
            /* Only an event name is provided in config */
            $normalized['attach'] = [ [ 'event' => $options, 'method' => null, 'priority' => 1 ] ];
            return $normalized;

        }

        if (ArrayUtils::isHashTable($options)) {
            $normalized['attach'] = $this->normalizeEventsSpec($options);

            if (isset($options['lazy'])) {
                $normalized['lazy'] = $options['lazy'];
            }

            return $normalized;

        }

        $event = $method = null;
        $priority = 1;
        $lazy = false;

        foreach ($options as $opt) {

            if (is_array($opt)) {
                /* Must be event names */
                $event = $opt;

            } else if (is_string($opt)) {
                if (null === $event) {
                    /* first string found is assumed to be the event name */
                    $event = [ $opt ];
                } else {
                    /* second string found must be a method name. */
                    $method = $opt;
                }

            } else if (is_int($opt)) {
                /* Integer values must be priority */
                $priority = $opt;

            } else if (is_bool($opt)) {
                /* Lazy option is passed. */
                $lazy = $opt;
            }
        }

        foreach ($event as &$eventSpec) {
            $eventSpec = [ 'event' => $eventSpec, 'method' => $method, 'priority' => $priority ];
        }

        $normalized['attach'] = $event;
        $normalized['lazy']   = $lazy;

        return $normalized;
    }

    protected function normalizeEventsSpec($options)
    {
        $listenerPriority = isset($options['priority']) ? $options['priority'] : 1;
        $listenerMethod   = isset($options['method'])   ? $options['method']   : '__none__';
        $events = [];

        foreach ($options['events'] as $event => $spec) {


            $eventPriority = isset($spec['priority']) ? $spec['priority'] : $listenerPriority;

            if (is_int($event)) {
                $events[$listenerMethod][$eventPriority][] = $spec;

            } else if (is_int($spec)) {
                $events[$listenerMethod][$spec][] = $event;

            } else if (is_string($spec)) {
                $events[$spec][$eventPriority][] = $event;

            } else if (is_array($spec)) {
                if (isset($spec['method'])) {
                    if (!is_array($spec['method'])) {
                        $spec['method'] = [ $spec['method'] ];
                    }

                    foreach ($spec['method'] as $method => $methodPriority) {
                        if (is_int($method)) {
                            $events[$methodPriority][$eventPriority][] = $event;

                        } else if (is_int($methodPriority)) {
                            $events[$method][$methodPriority][] = $event;
                        }
                    }
                }
            }
        }

        $eventsSpec = [];
        foreach ($events as $method => $priorities) {
            foreach ($priorities as $priority => $event) {
                foreach ($event as $ev) {
                    $eventsSpec[] = [
                        'event'    => $ev,
                        'method'   => '__none__' == $method ? null : $method,
                        'priority' => $priority,
                    ];
                }
            }
        }

        return $eventsSpec;
    }
}
