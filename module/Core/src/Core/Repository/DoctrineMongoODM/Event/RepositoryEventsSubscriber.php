<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\EventSubscriber;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\RepositoryInterface;
use Core\Entity\AttachableEntityInterface;
use Core\Entity\AttachableEntityManager;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

class RepositoryEventsSubscriber implements EventSubscriber
{
    const postConstruct = 'postRepositoryConstruct';
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;
    
    /**
     * @var AttachableEntityManager
     */
    protected $attachableEntityManagerPrototype;
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }
    
    public function postRepositoryConstruct($eventArgs)
    {
        $repo = $eventArgs->getRepository();
        if ($repo instanceof RepositoryInterface) {
            $documentName = $repo->getDocumentName();
            $entity = new $documentName();
            
            if ($entity instanceof AttachableEntityInterface) {
                $this->injectAttachableEntityManager($entity);
            }
            
            $repo->setEntityPrototype($entity);
            $repo->init($this->services);
        }
    }
    
    /**
     * @param LifecycleEventArgs $eventArgs
     * @since 0.28
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getDocument();
        
        if ($entity instanceof AttachableEntityInterface) {
            $this->injectAttachableEntityManager($entity);
        }
    }
    
    /**
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array(self::postConstruct, Events::postLoad);
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return RepositoryEventsSubscriber
     */
    public static function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator);
    }
    
    /**
     * @param AttachableEntityInterface $entity
     * @since 0.28
     */
    protected function injectAttachableEntityManager(AttachableEntityInterface $entity)
    {
        if (! isset($this->attachableEntityManagerPrototype)) {
            $this->attachableEntityManagerPrototype = new AttachableEntityManager($this->services->get('repositories'));
        }
        
        $entity->setAttachableEntityManager(clone $this->attachableEntityManagerPrototype);
    }
}
