<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQueryFactory.php */
namespace Applications\Repository\Filter;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use \Zend\ServiceManager\Factory\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the PaginationQuery
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @author  Anthonius Munthi <me@itstoni.com>
 * @package Applications
 */
class PaginationQueryFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$auth  = $container->get('AuthenticationService');
		$filter = new PaginationQuery($auth);
		return $filter;
	}
	
	/**
	 * Creates pagination Service
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 *
	 * @param ContainerInterface $container
	 * @return PaginationQuery|mixed
	 * @internal param ServiceLocatorInterface $serviceLocator
	 */
    public function createService(ContainerInterface $container)
    {
		return $this($container,PaginationQuery::class);
    }
}
