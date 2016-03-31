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
use Zend\EventManager\ListenerAggregateInterface;
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

        if ($events instanceOf EventProviderInterface || method_exists($events, 'setEventPrototype')) {
            $event = $serviceLocator->has($config['event']) ? $serviceLocator->get($config['event']) : new $config['event']();
            $events->setEventPrototype($event);
        }
        else {
            $events->setEventClass($config['event']);
        }

        /*
         * [
         *  'listeners' => [
         *      listener => event,
         *      listener => [ event{, methodName}{, priority}{, lazy }],
         *      aggregate,
         *      aggregate => priority
         */

        $aggregate = false;

        foreach ($config['listeners'] as $name => $options) {
            $event = $method = null;
            $priority = 0;
            $lazy = false;

            if (is_int($name) || is_int($options)) {
                $name = is_int($name) ? $options : $name;
                $priority = is_int($options) ? $options : 0;

            } else if (is_string($options)) {
                $event = $options;

            } else {

                while ($opt = array_shift($options)) {
                    if (is_array($opt)) {
                        $event = $opt;

                    } else if (is_string($opt)) {
                        if (null === $event) {
                            $event = $opt;
                        } else {
                            $method = $opt;
                        }

                    } else if (is_int($opt)) {
                        $priority = $opt;

                    } else if (is_bool($opt)) {
                        $lazy = $opt;
                    }
                }
            }

            if ($lazy) {
                 $aggregate = $aggregate ?: $serviceLocator->get('Core/Listener/DeferredListenerAggregate');
                 $aggregate->setHook($event, $name, $method, $priority);

                 continue;
            }

            if ($serviceLocator->has($name)) {
                $listener = $serviceLocator->get($name);

            } else if (class_exists($name, true)) {
                $listener = new $name();

            } else {
                throw new \UnexpectedValueException(sprintf(
                    'Class or service %s does not exists. Cannot create listener instance.', $name
                ));
            }

            if ($listener instanceOf ListenerAggregateInterface) {
                $listener->attach($events);
                continue;
            }

            $callback = $method ? [ $listener, $method ] : $listener;
            $events->attach($event, $callback, $priority);

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