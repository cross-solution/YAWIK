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

class RepositoryEventsSubscriber implements EventSubscriber
{
    const postConstruct = 'postRepositoryConstruct';
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;
    
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
            //if ($entity instanceof ) {
            //    $entity->setRepository($repo);
            //}
            $repo->setEntityPrototype($entity);
            $repo->init($this->services);
        }
    }
    
    /**
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array(self::postConstruct);
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return RepositoryEventsSubscriber
     */
    public static function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator);
    }
}
