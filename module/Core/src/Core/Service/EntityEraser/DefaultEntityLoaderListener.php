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
 * Default entity loader.
 *
 * Registered with "Core/EntityEraser/Load/Events" at a low priority.
 *
 * Tries to load an entity from the given repository and id.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DefaultEntityLoaderListener 
{

    /**
     *
     *
     * @var NameFilter
     */
    private $nameFilter;

    /**
     * DefaultEntityLoaderListener constructor.
     *
     * @param NameFilter $nameFilter
     */
    public function __construct(NameFilter $nameFilter)
    {
        $this->nameFilter = $nameFilter;
    }

    /**
     * Loads an entity.
     *
     * Uses the event name as repository name, which is filtered through {@link NameFilter}.
     *
     * If the param 'id' is not set in the event, or no repository or entity can be found,
     * returns null.
     *
     * @param BaseEvent $event
     *
     * @return array|null
     */
    public function __invoke(BaseEvent $event)
    {
        if (!($id = $event->getParam('id'))) { return null; }

        $repositoryName = $this->nameFilter->filter($event->getName());

        try {
            $repository = $event->getRepository($repositoryName);
        } catch (\Exception $e) {
            return null;
        }

        if (!$repository) { return null; }

        $entity = $repository->find($id);

        return $entity ? [$entity] : null;
    }
}
