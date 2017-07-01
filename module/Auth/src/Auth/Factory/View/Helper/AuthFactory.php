<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\View\Helper;

use Auth\View\Helper\Auth;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 * @author Anthonius Munthi <me@itstoni.com>
 */
class AuthFactory implements FactoryInterface
{

    /**
     * Create an Auth view helper
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return Auth
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get('AuthenticationService');
        $helper = new Auth();
        $helper->setService($auth);
        return $helper;
    }
}
