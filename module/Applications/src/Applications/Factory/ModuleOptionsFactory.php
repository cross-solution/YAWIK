<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author bleek@cross-solution.de
 * @license   MIT
 */

namespace Applications\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Applications\Options\ModuleOptions;

/**
 * Creates an instance of options for applications
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create a ModuleOptions options
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new ModuleOptions(isset($config['application_options']) ? $config['application_options'] : array());
    }
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ModuleOptions::class);
    }
}
