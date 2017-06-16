<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AclFactory.php */
namespace Acl\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class AclFactory
 * @package Acl\Controller\Plugin
 */
class AclFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $acl      = $container->get('Acl');
        $auth     = $container->get('AuthenticationService');

        $plugin = new Acl($acl, $auth->getUser());
        return $plugin;
    }

    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), Acl::class);
    }
}
