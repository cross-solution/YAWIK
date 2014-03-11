<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** RepositoryServiceFactory.php */ 
namespace Core\Repository;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryServiceFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm      = $serviceLocator->get('Core/DocumentManager');
        $service = new RepositoryService($dm);
        
        return $service;
    }
}

