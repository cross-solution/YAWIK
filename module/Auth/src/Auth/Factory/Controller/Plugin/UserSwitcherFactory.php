<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Factory\Controller\Plugin;

use Auth\Controller\Plugin\UserSwitcher;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the UserSwitcher controller plugin.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class UserSwitcherFactory implements FactoryInterface
{

    /**
     * Create an UserSwitcher plugin.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return UserSwitcher
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth   = $container->get('AuthenticationService');
        $acl    = $container->get('ControllerPluginManager')->get('Acl');
        $plugin = new UserSwitcher($auth);

        $plugin->setAclPlugin($acl);

        return $plugin;
    }

    /**
     * Create an UserSwitcher plugin.
     *
     * @param \Zend\ServiceManager\AbstractPluginManager|ServiceLocatorInterface $serviceLocator
     *
     * @return UserSwitcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Auth/User/Switcher');
    }
}