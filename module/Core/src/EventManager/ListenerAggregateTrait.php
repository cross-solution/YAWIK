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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait as ZfListenerAggregateTrait;

/**
 * Extension of the ZF ListenerAggregateTrait.
 *
 * Lets you specify the events to attach as an array in a property named "events".
 * which must be an array of enumerated arrays where the entries represents the
 * arguments to an event managers' attach method.
 *
 * <pre>
 * protected $events = [
 *      [ event, callback, priority ]
 * ];
 *
 * </pre>
 *
 * an Event can be:
 * * An event name as string
 * * Multiple event names as enumerated array
 * * A string prefixed with '->': Call the method on this instance to get the event.
 *   e.g. '->getEventForSpecialCase' will call $this->getEventForSpecialCase() and use
 *        its return value as the event argument.
 * * A string prefixes with '::': Call the method on SELF statically.
 * * A string in the format 'FQCN::method' will call the static method specified.
 *
 * Alternatively - if you need to instantiate an event prior to attaching to it,
 * you may reimplement {@link eventsProvider()}, which should return the event specification array of arrays
 * as described above.
 *
 * Further, If you need to provide some additional custom logic in the attach method, you can still benefit
 * from this trait by calling {@link attachEvents()}.
 *
 * @property array $events
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
trait ListenerAggregateTrait
{
    use ZfListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority=1)
    {
        return $this->attachEvents($events);
    }

    /**
     * Provides the event specification array.
     *
     * @return array
     */
    protected function eventsProvider()
    {
        return property_exists($this, 'events') ? $this->events : [];
    }

    /**
     * Attachs the events to the provided event manager.
     *
     * @param EventManagerInterface $events
     * @param array                 $eventsSpec
     *
     * @return $this
     * @throws \UnexpectedValueException
     */
    public function attachEvents(EventManagerInterface $events, array $eventsSpec = null)
    {
        if (null === $eventsSpec) {
            $eventsSpec = $this->eventsProvider();
        }
		
        foreach ($eventsSpec as $spec) {
            if (!is_array($spec) || 2 > count($spec)) {
                throw new \UnexpectedValueException('Event specification must be an array with at least two entries: event name and method name.');
            }

            $event  = $spec[0];
            $method = $spec[1];
            $priority = isset($spec[2]) ? $spec[2] : 0;

            $this->listeners[] = $events->attach($event, [ $this, $method ], $priority);
        }

        return $this;
    }
}