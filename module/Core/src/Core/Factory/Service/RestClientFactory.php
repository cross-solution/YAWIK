<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory\Service;

use Core\Service\RestClient;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RestClientFactory
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @package Core\Factory\Service
 */
abstract class RestClientFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
	
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$this->serviceLocator = $container;
		$service = new RestClient($this->getUri(), $this->getConfig());
		return $service;
	}
	
	
	/**
     * Create the settings service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return RestClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator,RestClient::class);
    }

    /**
     * @return mixed
     */
    abstract protected function getUri();

    /**
     * @return mixed
     */
    abstract protected function getConfig();
}
