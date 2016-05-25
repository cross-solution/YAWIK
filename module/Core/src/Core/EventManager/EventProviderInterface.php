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
 */
interface EventProviderInterface 
{

    /**
     * Sets the event prototype.
     *
     * @param EventInterface $event
     *
     * @return self
     */
    public function setEventPrototype(EventInterface $event);

    /**
     * Gets a new event instance.
     *
     * The instance will be preconfigured with the event name
     * and/or parameters, if provided.
     *
     * $name and $target can be passed in the $params array under the keys
     * 'name' and 'target'.
     *
     * @param array|string|null $name
     * @param array|object|null $target
     * @param array $params
     *
     * @return EventInterface
     */
    public function getEvent($name = null, $target = null, array $params = []);
}