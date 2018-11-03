<?php

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Create new Config plugin
 *
 * @package Core\Controller\Plugin
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ConfigFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $plugin = new Config($config);
        
        return $plugin;
    }
}
