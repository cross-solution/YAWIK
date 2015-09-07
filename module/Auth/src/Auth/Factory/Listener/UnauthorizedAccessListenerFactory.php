<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Listener;

use Auth\Listener\UnauthorizedAccessListener as Listener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class UnauthorizedAccessListenerFactory implements FactoryInterface
{
    /**
     * Creates an instance of \Auth\View\Helper\Auth
     *
     * - Injects the AuthenticationService
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\View\Helper\Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config   = $serviceLocator->get('Config');
        $listener = new Listener();
         
        if (isset($config['view_manager'])) {
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }
            if (isset($config['view_manager']['unauthorized_template'])) {
                $listener->setExceptionTemplate($config['view_manager']['unauthorized_template']);
            }
        }
        return $listener;
    }
}
