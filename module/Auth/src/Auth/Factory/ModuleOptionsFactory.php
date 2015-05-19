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
use Zend\Stdlib\ArrayUtils;

class ModuleOptionsFactory  implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configArray = isset($config['auth_options']) ? $config['auth_options'] : array();
        if (array_key_exists('core_options', $config)) {
            $configArray = ArrayUtils::merge($configArray, $config['core_options']);
        }
        return new ModuleOptions($configArray);
    }
}