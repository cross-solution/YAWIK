<?php
/**
 * Cross Applicant Management
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
        $dm      = $serviceLocator->get('doctrine.documentmanager.odm_default');
        $service = new RepositoryService($dm);
        
        return $service;
    }
}

