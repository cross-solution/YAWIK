<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\AuthenticationService;

/**
 * HybridAuth authentication adapter factory
 */
class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Creates an instance of \Auth\Adapter\HybridAuth
     *
     * - injects the \HybridAuth
     * - injects the UserMapper fetched from the service manager.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\HybridAuth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository = $serviceLocator->get('repositories')->get('Auth/User');
        $auth       = new AuthenticationService($repository);
        return $auth;
    }
}
