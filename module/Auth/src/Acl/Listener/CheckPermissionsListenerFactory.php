<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Acl\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class CheckPermissionsListenerFactory implements FactoryInterface
{
    /**
     * Creates an instance of \Auth\View\Helper\Auth
     * 
     * - Injects the AuthenticationService
     * 
     * @param ServiceLocatorInterface $helpers
     * @return \Auth\View\Helper\Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $acl          = $serviceLocator->get('acl');
        $user         = $serviceLocator->get('AuthenticationService')->getUser();
        $config       = $serviceLocator->get('Config');
        $exceptionMap = isset($config['acl']['exceptions']) ? $config['acl']['exceptions'] : array(); 
        $listener = new CheckPermissionsListener($acl, $user, $exceptionMap);
        
        return $listener;
    }
}
