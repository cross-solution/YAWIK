<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Service;

use Auth\View\Helper\Auth;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class AuthViewHelperFactory implements FactoryInterface
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
    public function createService(ServiceLocatorInterface $helpers)
    {
        $auth = $helpers->getServiceLocator()->get('AuthenticationService');
        $helper = new Auth();
        $helper->setService($auth);
        return $helper;
    }
}
