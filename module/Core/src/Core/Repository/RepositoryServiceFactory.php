<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RepositoryServiceFactory.php */
namespace Core\Repository;

use Core\Repository\DoctrineMongoODM\PersistenceListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryServiceFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm      = $serviceLocator->get('Core/DocumentManager');
        $service = new RepositoryService($dm);

        /* Attach persistence listener */
        $application = $serviceLocator->get('Application');
        $events      = $application->getEventManager();
        $persistenceListener = new PersistenceListener($service);
        $persistenceListener->attach($events);

        return $service;
    }
}
