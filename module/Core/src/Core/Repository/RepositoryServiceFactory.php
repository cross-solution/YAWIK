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
use Zend\ServiceManager\Config;

class RepositoryServiceFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configArray = $serviceLocator->get('Config');
        $configArray = isset($configArray['repositories']) ? $configArray['repositories'] : array();
        $config      = new Config($configArray);
        $service     = new RepositoryService();
        $config->configureServiceManager($service);
        
        return $service;
    }
}

