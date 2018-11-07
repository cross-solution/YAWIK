<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Options\ModuleOptions;
use Zend\View\Helper\Asset;

/**
 * Class ModuleOptionsFactory
 * @package Core\Factory
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create an ModuleOptions
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $applicationConfig = $container->get('ApplicationConfig');
        $config = $container->get('Config');

        $config = array_merge($config, $applicationConfig);
        $options = new ModuleOptions(isset($config['core_options']) ? $config['core_options'] : array());

        /* @TODO: make asset helper file to be configurable */
        $file = $options->getPublicDir().'/build/manifest.json';
        if (is_file($file)) {
            /* @var \Zend\View\Helper\Asset $assetHelper */
            $map = json_decode(file_get_contents($file), true);
            $assetHelper = $container->get('ViewHelperManager')->get('asset');
            $assetHelper->setResourceMap($map);
        }
        return $options;
    }
}
