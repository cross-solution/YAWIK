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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class EventManager extends ZfEventManager implements EventProviderInterface
{

    /**
     * The event prototype.
     *
     * @var EventInterface
     */
    protected $eventPrototype;

    public function setEventPrototype(EventInterface $event)
    {
        $this->eventPrototype = $event;
        $this->setEventClass(get_class($event));

        return $this;
    }

    public function getEvent($name = null, array $params = [])
    {
        if (!$this->eventPrototype) {
            $this->setEventPrototype(new Event());
        }

        $event = clone $this->eventPrototype;

        if (is_array($name)) {
            $params = $name;
            $name = isset($name['name']) ? $name['name'] : null;
            unset($params['name']);
        }

        $event->setName($name);
        $event->setParams($params);

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
            $e = $this->getEvent($event, $argv);
            $e->setTarget($target);

            return parent::trigger($e, $callback);
        }

        return parent::trigger($event, $target, $argv, $callback);
    }


}