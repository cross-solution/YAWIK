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
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RepositoryServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dm      = $container->get('Core/DocumentManager');
        $service = new RepositoryService($dm);
        
        /* Attach persistence listener */
        $application = $container->get('Application');
        $events      = $application->getEventManager();
        $persistenceListener = new PersistenceListener($service);
        $persistenceListener->attach($events);
        
        return $service;
    }
}
