<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceLocator for Paginators
 *
 * Class PaginatorServiceFactory
 * @package Core\Paginator
 */
class PaginatorServiceFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$configArray = $container->get('Config');
		$configArray = isset($configArray['paginator_manager']) ? $configArray['paginator_manager'] : array();
		$config      = new PaginatorServiceConfig($configArray);
		$service   = new PaginatorService($container,$config->toArray());
		
		return $service;
	}
}
