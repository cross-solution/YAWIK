<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Install\Factory\Controller\Plugin;

use Install\Controller\Plugin\YawikConfigCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for a YawikConfigCreator plugin instance.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class YawikConfigCreatorFactory implements FactoryInterface
{
    /**
     * Create a YawikConfigCreator controller plugin
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return YawikConfigCreator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $filters         = $container->get('FilterManager');
        $dbNameExtractor = $filters->get('Install/DbNameExtractor');

        $plugin = new YawikConfigCreator($dbNameExtractor);

        return $plugin;
    }
}
