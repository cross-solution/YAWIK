<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsResourceEventsSubscriber.php */
namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Core\Entity\PermissionsResourceInterface;
use Core\Entity\PermissionsInterface;
use Core\Entity\PermissionsAwareInterface;

/**
 * how to use:
 * derive a class from this, put in the repository
 *
 * Class AbstractUpdatePermissionsSubscriber
 * @package Core\Repository\DoctrineMongoODM\Event
 */
abstract class AbstractUpdatePermissionsSubscriber implements EventSubscriber
{
    protected $repositoryName;
    protected $queuedEntities;
    
    public function getSubscribedEvents()
    {
        return array(Events::postRemove, Events::postUpdate, 'postCommit');
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updatePermissions($args, true);
    }
    
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->updatePermissions($args, PermissionsInterface::PERMISSION_NONE);
    }

    public function postCommit(EventArgs $args)
    {
        if (!$this->queuedEntities) {
            return;
        }

        $dm = $args->get('documentManager'); /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        foreach ($this->queuedEntities as $entity) {
            $dm->persist($entity);
        }

        $this->queuedEntities = null;
        $dm->flush();
    }
    
    protected function updatePermissions(LifecycleEventArgs $args, $permission)
    {
        $resource = $args->getDocument();
        if (!$resource instanceof PermissionsResourceInterface) {
            return;
        }
        
        $entities = $this->getEntities($args);
        
        foreach ($entities as $entity) {
            if (!$entity instanceof PermissionsAwareInterface) {
                continue;
            }
            $permissions = $entity->getPermissions();
            $permissions->grant($resource, $permission);
            $this->queuedEntities[] = $entity;
        }
        
        //$args->getDocumentManager()->flush($resource);
    }

    /**
     * Fetch entities from the database which permissions are assigned the referral document.
     *
     * @param LifecycleEventArgs $args
     *
     * @return Collection
     */
    protected function getEntities($args)
    {
        $dm             = $args->getDocumentManager();
        $resource       = $args->getDocument();
        $repositoryName = $this->getRepositoryName();
        $resourceId     = $resource->getPermissionsResourceId();
        $repository     = $dm->getRepository($repositoryName);
        
        $criteria = array(
            'permissions.assigned.' . $resourceId => array(
                '$exists' => true
            )
        );
        
        $entities = $repository->findBy($criteria);

        return $entities;
    }
    
    protected function getRepositoryName()
    {
        if (0 === strpos($this->repositoryName, '\\')) {
            return $this->repositoryName;
        }
        
        if (null === $this->repositoryName) {
            throw new \RuntimeException('RepositoryName is missing. Define $this->repositoryName.');
        }
        
        $nameParts = explode('/', $this->repositoryName);
        if (2 > count($nameParts)) {
            $nameParts[1] = $nameParts[0];
        }
        
        $namespace  = $nameParts[0];
        $entityName = $nameParts[1];
        
        $this->repositoryName = "\\$namespace\\Entity\\$entityName";
        return $this->repositoryName;
    }
}
