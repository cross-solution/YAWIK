<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Factory\Controller\Plugin;

use Install\Controller\Plugin\UserCreator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for an UserCreator plugin instance
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class UserCreatorFactory implements FactoryInterface
{
    /**
     * Creates a UserCreator plugin instance.
     *
     * @param ServiceLocatorInterface $serviceLocator Controller plugin manager
     *
     * @return UserCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\PluginManager */
        $services = $serviceLocator->getServiceLocator();
        $filters  = $services->get('FilterManager');

        $dbNameExctractor = $filters->get('Install/DbNameExtractor');
        $credentialFilter = $filters->get('Auth/CredentialFilter');

        $plugin = new UserCreator($dbNameExctractor, $credentialFilter);

        return $plugin;
    }
}
