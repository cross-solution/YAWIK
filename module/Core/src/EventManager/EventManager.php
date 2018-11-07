<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\EventManager;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager as ZfEventManager;

/**
 * EventManager extension which allows creating event instances.
 *
 * Also allows calling of trigger() and triggerUntil() with event instances.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 * @since 0.30 - ZF3 compatibility
 */
class EventManager extends ZfEventManager implements EventProviderInterface
{
    public function getEvent($name = null, $target = null, $params = null)
    {
        $event = clone $this->eventPrototype;

        if (is_array($name)) {
            $params = $name;
            $name = null;
        } elseif (is_array($target)) {
            $params = $target;
            $target = null;
        }

        if (!$name && isset($params['name'])) {
            $name = $params['name'];
            unset($params['name']);
        }

        if (!$target && isset($params['target'])) {
            $target = $params['target'];
            unset($params['target']);
        }

        $event->setName($name);
        if (null !== $target) {
            $event->setTarget($target);
        }
        if (null !== $params) {
            $event->setParams($params);
        }

        return $event;
    }
    
    /**
     * Trigger an event.
     *
     * If no event instance is passed, it creates one prior to triggering.
     *
     * @param EventInterface|string $eventName
     * @param object|string|null $target
     * @param array $argv
     *
     * @return \Zend\EventManager\ResponseCollection
     */
    public function trigger($eventName, $target = null, $argv = [])
    {
        $event = $eventName instanceof EventInterface
            ? $eventName
            : $this->getEvent($eventName, $target, $argv);

        return $this->triggerListeners($event);
    }

    /**
     * Trigger an event, applying a callback to each listener's result
     *
     * If no event instance is passed, it creates one prior to triggering.
     *
     * @param callable $callback
     * @param EventInterface|string   $eventName
     * @param object|string|null     $target
     * @param array    $argv
     *
     * @return \Zend\EventManager\ResponseCollection
     */
    public function triggerUntil(callable $callback, $eventName, $target = null, $argv = [])
    {
        $event = $eventName instanceof EventInterface
            ? $eventName
            : $this->getEvent($eventName, $target, $argv);

        return $this->triggerListeners($event, $callback);
    }
}
