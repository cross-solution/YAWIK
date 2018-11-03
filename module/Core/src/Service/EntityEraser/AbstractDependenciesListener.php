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

/**
 * Boilerplate for a Dependency Listener.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
abstract class AbstractDependenciesListener
{
    /**
     * List of entity class names this listener should react to.
     *
     * an empty array lets this listener react to all entity classes.
     *
     * @var array|string[]
     */
    protected $entityClasses = [];

    /**
     * Callback for the event manager.
     *
     * Checks the entity class and calls either
     * {@link dependencyCheck() } or {@link onDelete() }
     *
     * @param DependencyResultEvent $event
     *
     * @return null|DependencyResult[]|DependencyResult|array|\Traversable
     */
    public function __invoke(DependencyResultEvent $event)
    {
        if (!$this->checkEntityClasses($event->getEntity())) {
            return null;
        }

        return DependencyResultEvent::CHECK_DEPENDENCIES == $event->getName()
            ? $this->dependencyCheck($event)
            : $this->onDelete($event)
        ;
    }

    /**
     * Called upon an CHECK_DEPENDENCIES event.
     *
     * Gather affected entities and returns it in a way the event manager understands or
     * adds the result directly to the events' DependencyResultCollection
     *
     * @param DependencyResultEvent $event
     *
     * @return null|DependencyResult[]|DependencyResult|array|\Traversable
     */
    abstract protected function dependencyCheck(DependencyResultEvent $event);

    /**
     * Called upon an DELETE event.
     *
     * Deletes all dependent entities and returns them as a DependencyResult
     * (or sets them in the event)
     *
     * Proxies to {@link dependencyCheck()} per default, so you can handle both events
     * in the same method, if you wish.
     *
     * @param DependencyResultEvent $event
     *
     * @return array|DependencyResult|DependencyResult[]|null|\Traversable
     */
    protected function onDelete(DependencyResultEvent $event)
    {
        return $this->dependencyCheck($event);
    }

    private function checkEntityClasses($entity)
    {
        if (empty($this->entityClasses)) {
            return true;
        }

        foreach ($this->entityClasses as $class) {
            if ($entity instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
