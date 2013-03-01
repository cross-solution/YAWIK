<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Service;

use Auth\View\Helper\Auth;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Hybrid_Auth instance.
 */
class AuthViewHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $auth = $services->getServiceLocator()->get('AuthenticationService');
        $helper = new Auth();
        $helper->setService($auth);
        return $helper;
    }
}
