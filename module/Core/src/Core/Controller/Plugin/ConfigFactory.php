<?php

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Settings\Repository\Settings;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Core\Controller\Plugin\Config;

class ConfigFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$config = $container->get('Config');
		$plugin = new Config($config);
		
		return $plugin;
	}
	
	/**
     * Create the settings service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Config
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator,Config::class);
    }
}
