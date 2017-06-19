<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Acl\Assertion;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Config;

/**
 * Factory for creating the AssertionManager.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AssertionManagerFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null ) {
		$configContainer = $container->get('Config');
		$configArray = isset($configContainer['acl']['assertions'])
			? $configContainer['acl']['assertions']
			: array();

		$manager     = new AssertionManager($container, $configArray);

		
		$manager->configure(['shared_by_default'=>false]);
		return $manager;
	}
	
	/**
     * Creates an instance of \Auth\View\Helper\Auth
     *
     * - Injects the AuthenticationService
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AssertionManager
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
		return $this($serviceLocator,AssertionManager::class);
    }
}
