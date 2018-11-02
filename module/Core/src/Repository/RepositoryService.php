<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RepositoryService.php */
namespace Core\Repository;

use Core\Repository\DoctrineMongoODM\Event\EventArgs;
use Doctrine\ODM\MongoDB\DocumentManager;
use Core\Entity\EntityInterface;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

class RepositoryService
{
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }
    
    public function get($name)
    {
        if (!class_exists($name)) {
            $nameParts = explode('/', $name);
            if (2 > count($nameParts)) {
                $nameParts = array($name, substr($name, 0, -1));
                //throw new \InvalidArgumentException('Name must be in the format "Namespace/Entity")');
            }
        
            $namespace   = $nameParts[0];
            $entityName  = $nameParts[1];
            $name = "\\$namespace\\Entity\\$entityName";
        }
        
        $repository  = $this->dm->getRepository($name);

        if (!$repository instanceof AbstractRepository) {
            $eventArgs = new DoctrineMongoODM\Event\EventArgs(
                array(
                    'repository' => $repository,
                )
            );
            $this->dm->getEventManager()->dispatchEvent(DoctrineMongoODM\Event\RepositoryEventsSubscriber::postConstruct, $eventArgs);
        }

        return $repository;
    }
    
    public function createQueryBuilder()
    {
        return $this->dm->createQueryBuilder();
    }

    public function store(EntityInterface $entity)
    {
        $this->dm->persist($entity);
        $this->dm->flush($entity);
        return $this;
    }

    public function flush($entity = null, array $options = array())
    {
        $this->dm->flush($entity);

        $events = $this->dm->getEventManager();
        $events->hasListeners('postCommit')
        && $events->dispatchEvent('postCommit', new EventArgs(array('document' => $entity, 'documentManager' => $this->dm)));
    }

    public function remove(EntityInterface $entity, $flush=false)
    {
        $dm     = $this->dm;
        $events = $dm->getEventManager();
        
        $dm->remove($entity);
        
        $events->hasListeners('postRemoveEntity')
        && $events->dispatchEvent('postRemoveEntity', new LifecycleEventArgs($entity, $dm));

        if ($flush) {
            $dm->flush();
        }
        
        return $this;
    }
    
    public function detach(EntityInterface $entity)
    {
        $this->dm->detach($entity);
        return $this;
    }
    
    public function __call($method, $params)
    {
        $callback = array($this->dm, $method);
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } else {
            throw new \BadMethodCallException('Method not exists for this class.');
        }
    }
}
