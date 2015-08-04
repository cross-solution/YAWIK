<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Factory\Controller\Plugin;

use Install\Controller\Plugin\YawikConfigCreator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for a YawikConfigCreator plugin instance.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class YawikConfigCreatorFactory implements FactoryInterface
{
    /**
     * Creates a YawikConfigCreator plugin instance.
     *
     * @param ServiceLocatorInterface|\Zend\Mvc\Controller\PluginManager $serviceLocator
     *
     * @return YawikConfigCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services        = $serviceLocator->getServiceLocator();
        $filters         = $services->get('FilterManager');
        $dbNameExtractor = $filters->get('Install/DbNameExtractor');

        $plugin = new YawikConfigCreator($dbNameExtractor);

        return $plugin;
    }
}