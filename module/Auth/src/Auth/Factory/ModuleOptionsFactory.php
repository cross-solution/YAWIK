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
        if (array_key_exists('core_options', $config) && array_key_exists('siteName', $config['core_options'])) {

            $configArray = ArrayUtils::merge($configArray, array('siteName' => $config['core_options']['siteName']));
        }
        return new ModuleOptions($configArray);
    }
}