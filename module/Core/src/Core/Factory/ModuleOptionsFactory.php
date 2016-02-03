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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Options\ModuleOptions;

/**
 * Class ModuleOptionsFactory
 * @package Core\Factory
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ModuleOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new ModuleOptions(isset($config['core_options']) ? $config['core_options'] : array());
    }
}
