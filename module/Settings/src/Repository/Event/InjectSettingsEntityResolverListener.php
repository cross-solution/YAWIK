<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Settings\Repository\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Auth\Entity\UserInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectSettingsEntityResolverListener implements EventSubscriber
{
    
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
    
    /**
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array(Events::postLoad);
    }
    
    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        if (!$document instanceof UserInterface) {
            return;
        }
        
        $resolver = $this->services->get('Settings/EntityResolver');
        $document->setSettingsEntityResolver($resolver);
        
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return InjectSettingsEntityResolverListener
     */
    public static function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator);
    }
}
