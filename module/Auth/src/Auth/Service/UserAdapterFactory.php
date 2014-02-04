<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Adapter\User;

/**
 * authentication adapter factory
 */
class UserAdapterFactory implements FactoryInterface 
{

    /**
     * Creates an instance of \Auth\Adapter\ExternalApplication
     * 
     * - injects the UserRepository fetched from the service manager.
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\ExternalApplication
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository = $serviceLocator->get('repositories')->get('Auth/User');
        $adapter = new User($repository);
        
        return $adapter;
    }
    
}