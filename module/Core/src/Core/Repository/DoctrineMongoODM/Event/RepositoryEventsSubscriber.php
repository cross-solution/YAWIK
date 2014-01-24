<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** RepositoryCreated.php */ 
namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\EventSubscriber;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Repository\RepositoryInterface;

class RepositoryEventsSubscriber implements EventSubscriber, ServiceLocatorAwareInterface
{
    const postConstruct = 'postRepositoryConstruct';
    
    protected $services;
    
    public function getServiceLocator ()
    {
        return $this->services;
    }
    
    public function setServiceLocator (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
        return $this;
    }
    
    public function postRepositoryConstruct($eventArgs)
    {
        $repo = $eventArgs->getRepository();
        if ($repo instanceOf RepositoryInterface) {
            $documentName = $repo->getDocumentName();
            $entity = new $documentName();
            $repo->setEntityPrototype($entity);
            $repo->init($this->services);
        }
    }
    
    public function getSubscribedEvents()
    {
        return array(self::postConstruct);
    }
	

}

