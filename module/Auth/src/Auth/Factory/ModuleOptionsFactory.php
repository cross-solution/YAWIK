<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Options\ModuleOptions;

/**
 * Creates the Auth Options
 *
 * Class ModuleOptionsFactory
 * @package Auth\Factory
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create an ModuleOptions options
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ModuleOptions
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $configArray = isset($config['auth_options']) ? $config['auth_options'] : array();
        $options = new ModuleOptions($configArray);

        if ("" == $options->getFromName()) {
            /* @var $coreOptions \Core\Options\ModuleOptions */
            $coreOptions = $container->get('Core\Options');
            $options->setFromName($coreOptions->getSiteName());
        }

        return new ModuleOptions($configArray);
    }
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ModuleOptions::class);
    }
}
