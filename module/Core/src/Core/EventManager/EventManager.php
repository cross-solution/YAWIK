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

use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager as ZfEventManager;
use Zend\EventManager\Exception;

/**
 * EventPrototype Aware EventManager implementation.
 *
 * @internal
 *      Will be obsolete with ZF3 as ZF3s' EventManager implementation is
 *      already event prototype aware.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class EventManager extends ZfEventManager implements EventProviderInterface
{

    public function getEvent($name = null, $target = null, $params = null)
    {
        if (!$this->eventPrototype) {
            $this->setEventPrototype(new Event());
        }

        $event = clone $this->eventPrototype;

        if (is_array($name)) {
            $params = $name;
            $name = null;

        } else if (is_array($target)) {
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
        if (null !== $target) { $event->setTarget($target); }
        if (null !== $params) { $event->setParams($params); }

        return $event;
    }

    public function trigger($event, $target = null, $argv = [], $callback = null)
    {
        if (!$event instanceOf EventInterface
            && !$target instanceOf EventInterface
            && !$argv instanceOf EventInterface
        ) {
            /*
             * Create the event from the prototype, and not
             * from eventClass as the parent implementation does.
             */
            $e = $this->getEvent($event, $target, $argv);

            return parent::trigger($e, $callback);
        }

        return parent::trigger($event, $target, $argv, $callback);
    }


}