<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Factory\Controller\Plugin;

use Auth\Entity\Filter\CredentialFilter;
use Install\Controller\Plugin\UserCreator;
use Install\Filter\DbNameExtractor;
use Interop\Container\ContainerInterface;
use Zend\Filter\FilterPluginManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
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
     * Create a UserCreator controller plugin
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserCreator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $filters = $container->get('FilterManager');

        $dbNameExctractor = $filters->get(DbNameExtractor::class);
        $credentialFilter = $filters->get(CredentialFilter::class);

        $plugin = new UserCreator($dbNameExctractor, $credentialFilter);

        return $plugin;
    }

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
        return $this($serviceLocator, UserCreator::class);
    }
}
