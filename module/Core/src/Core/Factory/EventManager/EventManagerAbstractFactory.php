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

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class EventManagerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return 0 === strpos(strrev($requestedName), 'stnevE/');
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator, $requestedName);
        $events = $serviceLocator->get($config['service']);

        $events->setIdentifiers($config['identifiers']);

        if (method_exists($events, 'setEventPrototype')) {
            $event = $serviceLocator->has($config['event']) ? $serviceLocator->get($config['event']) : new $config['event']();
            $events->setEventPrototype($event);
        }
        else {
            $events->setEventClass($config['event']);
        }

        /*
         * [
         *  'listeners' => [
         *      'listenerclassorservice' => event,
         *      'listenerclassorservice' => [ event, event => priority ],
         *      'listeneraggregate',
         *      'listeneraggregate' => priority
         * ]
         * [
         *  'listeners' => [
         *      'event' => [
         *              listener,
         *              listener => priority,      (= [ 'priority' => priority ] )
         *              listener => 'methodName',  (= [ 'method' => methodName ] )
         *              listener => bool,          (= [ 'lazy' => bool ] )
         *              listener => [ 'method' => name, 'priority' => priority, 'lazy' => bool ]
         *      ],
         *      ...,
         *      '*' => [
         *              aggragte,
         *              aggregate => $priority,
         *              aggregate => [ 'priority' => priority ]
         *     ],
         * ]
         */

        $aggregate = false;

        foreach ($config['listeners'] as $event => $listeners) {
            $isAggregate = '*' == $event;

            foreach ($listeners as $listener => $options) {
                /* Normalize options */
                if (is_int($listener)) {
                    $listener = $options;
                    $options = [];

                } else if (is_int($options)) {
                    $options = [ 'priority' => $options ];

                } else if (is_string($options)) {
                    $options = [ 'method' => $options ];

                } else if (!is_array($options)) {
                    $options = [ 'lazy' => (bool) $options ];

                }

                $options = array_merge([ 'method' => null, 'priority' => 0, 'lazy' => false ], $options);

                if ($options['lazy'] && !$isAggregate) {
                    $aggregate = $aggregate ?: $serviceLocator->get('Core/Listener/DeferredListenerAggregate');
                    $aggregate->setHook($event, $listener, $options['method'], $options['priority']);

                    continue;
                }

                if ($serviceLocator->has($listener)) {
                    $listener = $serviceLocator->get($listener);

                } else if (class_exists($listener, true)) {
                    $listener = new $listener();

                } else {
                    throw new \UnexpectedValueException(sprintf(
                        'Class or service %s does not exists. Cannot create listener instance.', $listener
                    ));
                }

                if ($isAggregate) {
                    $listener->attach($events);
                } else {
                    $callback = $options['method'] ? [ $listener, $options['method'] ] : $listener;
                    $events->attach($event, $callback, $options['priority']);
                }

            }
        }

        if ($aggregate) {
            $aggregate->attach($events);
        }

        return $events;
    }

    protected function getConfig($services, $name)
    {
        $defaults = [
            'service' => 'EventManager',
            'identifiers' => [ $name ],
            'event' => '\Zend\EventManager\Event',
            'listeners' => [],
        ];

        $config = $services->get('Config');
        $config = isset($config['event_manager'][$name]) ? $config['event_manager'][$name] : [];

        $config = array_replace_recursive($defaults, $config);

        return $config;
    }

}