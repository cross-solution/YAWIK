<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Service\EntityEraser;

use Core\EventManager\EventManager as CoreEventManager;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * EventManager for Dependency events.
 *
 * Handles the repsonses from the listeners and add them to the events' dependency result collection.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class EntityEraserEvents extends CoreEventManager
{
    /**
     * Set the event prototype.
     *
     * Only instances of DependencyResultEvent are allowed.
     *
     * @param EventInterface $prototype
     * @throws \InvalidArgumentException
     */
    public function setEventPrototype(EventInterface $prototype)
    {
        if (!$prototype instanceof DependencyResultEvent) {
            throw new \InvalidArgumentException('This event manager only accepts events of the type ' . DependencyResultEvent::class);
        }

        parent::setEventPrototype($prototype);
    }

    /**
     * Triggers listeners.
     *
     * Loops over the responses and tries to add any non empty response as a DepnendecyResult to the collection.
     *
     * @param EventInterface $event
     * @param callable|null  $callback
     *
     * @return \Zend\EventManager\ResponseCollection
     * @throws \InvalidArgumentException if the event is not an instance of DependencyResultEvent
     */
    protected function triggerListeners(EventInterface $event, callable $callback = null)
    {
        if (!$event instanceof DependencyResultEvent) {
            throw new \InvalidArgumentException('This event manager only accepts events of the type ' . DependencyResultEvent::class);
        }

        $results = parent::triggerListeners($event, $callback);

        $dependencies = $event->getDependencyResultCollection();

        foreach ($results as $result) {
            if (null !== $result) {
                try {
                    $dependencies->add($result);
                }
                /* silently ignore all invalid results */
                catch (\UnexpectedValueException $e) {
                } catch (\InvalidArgumentException $e) {
                }
            }
        }

        return $results;
    }
}
