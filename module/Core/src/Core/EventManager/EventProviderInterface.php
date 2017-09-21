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

/**
 * EventProvider interface
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 * @since 0.30 - remove setEventPrototype() (now in ZF3 EventManagerInterface)
 */
interface EventProviderInterface 
{
    /**
     * Gets a new event instance.
     *
     * The instance will be preconfigured with the event name
     * and/or parameters, if provided.
     *
     * $name and/or $target can be passed in the $params array under the keys
     * 'name' and 'target'. The $params must then be passed as first or second argument.
     *
     * @param array|string|null $name
     * @param array|object|null $target
     * @param array|\Traversable|null $params
     *
     * @return EventInterface
     */
    public function getEvent($name = null, $target = null, $params = null);
}
