<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsResourceEventsSubscriber.php */ 
namespace Core\Repository\DoctrineMongoODM\Event;

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
    
    public function getSubscribedEvents() {
        return array(Events::postRemove, Events::postUpdate);
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updatePermissions($args, true);
    }
    
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->updatePermissions($args, PermissionsInterface::PERMISSION_NONE);
    }
    
    protected function updatePermissions(LifecycleEventArgs $args, $permission)
    {
        $resource = $args->getDocument();
        if (!$resource instanceOf PermissionsResourceInterface) {
            return;
        }
        
        $entities = $this->getEntities($args);
        
        foreach ($entities as $entity) {
            if (!$entity instanceOf PermissionsAwareInterface) {
                continue;
            }
            $permissions = $entity->getPermissions();
            $permissions->grant($resource, $permission);
        }
        
        $args->getDocumentManager()->flush();
        
    }

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
            $nameParts = array($name, $name);
        }
        
        $namespace  = $nameParts[0];
        $entityName = $nameParts[1];
        
        $this->repositoryName = "\\$namespace\\Entity\\$entityName";
        return $this->repositoryName;
        
    }
    
}

