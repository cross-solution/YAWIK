<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        $config     = $serviceLocator->get('Config');
        $config     = isset($config['Auth']['default_user']) ? $config['Auth']['default_user'] : array();
        $repository = $serviceLocator->get('repositories')->get('Auth/User');
        
        $adapter = new User($repository);
        
        if (isset($config['login']) && !empty($config['login'])
            && isset($config['password']) && !empty($config['password'])
        ) {
            $adapter->setDefaultUser($config['login'], $config['password']);
        }
        
        return $adapter;
    }
    
}