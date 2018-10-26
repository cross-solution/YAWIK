<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller\Plugin;

use Core\Entity\EntityInterface;
use Core\EventManager\EventManager;
use Core\Repository\RepositoryService;
use Core\Service\EntityEraser\DependencyResultEvent;
use Core\Service\EntityEraser\EntityEraserEvents;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Plugin to load entities, check their dependencies and delete these entities with all their dependencies.
 *
 * It's meant to use three steps:
 *
 * 1. Load the entities to delete.
 *    Using the event manager "Core/EntityEraser/Load/Events", it loads entities to delete by
 *    triggering the event with the event named after a passed in key.
 *    This way all kind of entities can be loaded (mostly relevant for the console action "purge".)
 *
 * 2. Check dependencies.
 *    Using the event manager "Core/EntityEraser/Dependencies/Events", it triggers an event for
 *    each entity to delete. This way any listener can add dependencies to the DependencyResultCollection.
 *    These dependencies can then be used to show which entities will be affected in which way, if the entities
 *    will be deleted.
 *
 * 3. Delete the entities.
 *    Using the event manager "Core/EntityEraser/Dependencies/Events", for each entity to delete, the event
 *    DependencyResultEvent::DELETE will be fired. Allowing listeners to act acording to the deletion of the entity.
 *    After that, each entity will be removed from the database.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class EntityEraser extends AbstractPlugin
{
    /**
     * @var EntityEraserEvents
     */
    private $entityEraserEvents;

    /**
     * EventManager for the loading of entities.
     *
     * @var EventManager
     */
    private $loadEntitiesEvents;

    /**
     * RepositoryService
     *
     * @var \Core\Repository\RepositoryService
     */
    private $repositories;

    /**
     * Array of options to be passed along to listeners as event parameters.
     *
     * @var array
     */
    private $options = [];

    /**
     * EntityEraser constructor.
     *
     * @param EntityEraserEvents $entityEraserEvents
     * @param EventManager       $loadEntitiesEvents
     * @param RepositoryService  $repositories
     */
    public function __construct(EntityEraserEvents $entityEraserEvents, EventManager $loadEntitiesEvents, RepositoryService $repositories)
    {
        $this->entityEraserEvents = $entityEraserEvents;
        $this->loadEntitiesEvents = $loadEntitiesEvents;
        $this->repositories = $repositories;
    }

    /**
     * Set options to be passed along to listeners as event parameters.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Loads entities to be deleted.
     *
     * Triggers an event on the "Core/EntityEraser/Load/Events" event manager.
     *
     * @param string      $entity Used as event name.
     * @param string|null $id
     *
     * @return array|\Traversable|null
     */
    public function loadEntities($entity, $id = null)
    {
        $params = $this->options;
        $params['id'] = $id;
        $params['repositories'] = $this->repositories;

        $event = $this->loadEntitiesEvents->getEvent($entity, $this, $params);
        $responses = $this->loadEntitiesEvents->triggerEventUntil(
            function ($response) { return (is_array($response) || $response instanceOf \Traversable) && count($response); },
            $event
        );

        $entities = $responses->last();

        return $entities;
    }

    /**
     * Checks dependencies for an entity.
     *
     * Triggers the DependencyResultEvent::CHECK_DEPENDENCIES event on the
     * "Core/EntityEraser/Dependencies/Events" event manager.
     *
     * @param EntityInterface $entity
     *
     * @return \Core\Service\EntityEraser\DependencyResultCollection
     * @uses triggerEvent
     */
    public function checkDependencies(EntityInterface $entity)
    {
        return $this->triggerEvent(DependencyResultEvent::CHECK_DEPENDENCIES, $entity);
    }

    /**
     * Deletes an entity.
     *
     * Triggers the DependencyResultEvent::DELETE event on the
     * "Core/EntityEraser/Dependencies/Events" event manager.
     *
     * Removes the entity from the database.
     *
     * @param EntityInterface $entity
     *
     * @return \Core\Service\EntityEraser\DependencyResultCollection
     * @uses triggerEvent
     */
    public function erase(EntityInterface $entity)
    {
        $dependencies = $this->triggerEvent(DependencyResultEvent::DELETE, $entity);
        $this->repositories->remove($entity);

        return $dependencies;
    }

    /**
     * Helper function to trigger a DependecyResultEvent.
     *
     * @param string $name
     * @param EntityInterface $entity
     *
     * @return \Core\Service\EntityEraser\DependencyResultCollection
     */
    private function triggerEvent($name, EntityInterface $entity)
    {
        $params = $this->options;
        $params['entity'] = $entity;
        $params['repositories'] = $this->repositories;

        /* @var DependencyResultEvent $event */
        $event = $this->entityEraserEvents->getEvent($name, $this, $params);

        $this->entityEraserEvents->triggerEvent($event);
        $dependencies = $event->getDependencyResultCollection();

        return $dependencies;
    }
}
