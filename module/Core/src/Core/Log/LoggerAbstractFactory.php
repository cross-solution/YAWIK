<?php
/**
 * YAWIK
 *
 * (this file is taken from ZF 2.2)
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LoggerAbstractFactory.php */
namespace Core\Log;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;

/**
 * Logger abstract service factory.
 *
 * Allow to configure multiple loggers for application.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 */
class LoggerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Configuration key holding logger configuration
     *
     * @var string
     */
    protected $configKey = 'log';

    /**
     * Create a new Logger instance
     *
     * @param ContainerInterface        $container
     * @param string                    $requestedName
     * @param array|null                $options
     * @return Logger
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $this->getConfig($container);
        $config  = $config[$requestedName];
        if (is_string($config) || isset($config['service'])) {
            $serviceName = is_string($config) ? $config : $config['service'];
            return $container->get($serviceName);
        }
        $this->processConfig($config, $container);
        return new Logger($config);
    }

    /**
     * Check if the factory can create an instance for the given $requestedName service
     *
     * @param ContainerInterface        $container
     * @param string                    $requestedName
     * @param array|null                $options
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return isset($config[$requestedName]);
    }

    /**
     * Determines if we can create a Logger instance with give $requestedName
     *
     * @param  ServiceLocatorInterface $services
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return $this->canCreate($services,$requestedName);
    }

    /**
     * Create a Logger instance with given $requestedName service
     *
     * @param  ServiceLocatorInterface $services
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return Logger
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return $this($services,$requestedName);
    }

    /**
     * Retrieve configuration for loggers, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }

    protected function processConfig(&$config, ServiceLocatorInterface $services)
    {
        if (!isset($config['writer_plugin_manager'])) {
            $config['writer_plugin_manager'] = $services->get('LogWriterManager');
        }
        if (!isset($config['processor_plugin_manager'])) {
            $config['processor_plugin_manager'] = $services->get('LogProcessorManager');
        }

        if (!isset($config['writers'])) {
            return;
        }

        foreach ($config['writers'] as $index => $writerConfig) {
            if (!isset($writerConfig['options']['db'])
            || !is_string($writerConfig['options']['db'])
            ) {
                continue;
            }
            if (!$services->has($writerConfig['options']['db'])) {
                continue;
            }

            // Retrieve the DB service from the service locator, and
            // inject it into the configuration.
            $db = $services->get($writerConfig['options']['db']);
            $config['writers'][$index]['options']['db'] = $db;
        }
    }
}
