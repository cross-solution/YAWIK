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
        $config = $container->get('config');

        return new ModuleOptions(isset($config['core_options']) ? $config['core_options'] : array());
    }
}
