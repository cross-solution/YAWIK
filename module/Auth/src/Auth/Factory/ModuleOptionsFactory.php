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
use Zend\ServiceManager\Factory\FactoryInterface;
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
	 * @param ContainerInterface $container
	 * @param string $requestedName
	 * @param array|null $options
	 *
	 * @return ModuleOptions
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
    public function createService(ContainerInterface $container)
    {
        return $this($container, ModuleOptions::class);
    }
}
