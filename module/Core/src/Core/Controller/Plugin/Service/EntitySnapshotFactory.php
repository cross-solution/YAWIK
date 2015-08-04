<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Controller\Plugin\Service;

use Core\Controller\Plugin\EntitySnapshot;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntitySnapshotFactory implements FactoryInterface
{
    protected $serviceLocator;

    /**
     * we need the Repository to store the snapshot
     * and the configuration to get the services for Hydrator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this|mixed
     */

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocatorMain = $serviceLocator->getServiceLocator();
        $entitySnapshotPlugin = new EntitySnapshot();
        // @TODO actually we just need...
        // an access to all options defining an Snapshot-Generator
        // the Hydrator-Manager
        $entitySnapshotPlugin->setServiceLocator($serviceLocatorMain);
        $repositories = $serviceLocatorMain->get('repositories');
        $entitySnapshotPlugin->setRepositories($repositories);
        return $entitySnapshotPlugin;
    }
}
