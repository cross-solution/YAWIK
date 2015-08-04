<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Options\ModuleOptions;

/**
 * Creates the Auth Options
 *
 * Class ModuleOptionsFactory
 * @package Auth\Factory
 */
class ModuleOptionsFactory  implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configArray = isset($config['auth_options']) ? $config['auth_options'] : array();
        $options = new ModuleOptions($configArray);

        if ("" == $options->getFromName()) {
            /* @var $coreOptions \Core\Options\ModuleOptions */
            $coreOptions = $serviceLocator->get('Core\Options');
            $options->setFromName($coreOptions->getSiteName());
        }

        return new ModuleOptions($configArray);
    }
}