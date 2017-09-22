<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Adapter;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Adapter\ExternalApplication;

/**
 * authentication adapter factory
 */
class ExternalApplicationAdapterFactory implements FactoryInterface
{
	
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$repository = $container->get('repositories')->get('Auth/User');
		$adapter = new ExternalApplication($repository);
		$adapter->setServiceLocator($container);
		$config  = $container->get('Config');
		if (isset($config['Auth']['external_applications']) && is_array($config['Auth']['external_applications'])) {
			$adapter->setApplicationKeys($config['Auth']['external_applications']);
		}
		
		return $adapter;
	}
	
	
	/**
     * Creates an instance of \Auth\Adapter\ExternalApplication
     *
     * - injects the UserRepository fetched from the service manager.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\ExternalApplication
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ExternalApplication::class);
    }
}
