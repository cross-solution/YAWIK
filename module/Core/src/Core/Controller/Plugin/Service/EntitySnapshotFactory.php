<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Controller\Plugin\Service;

use Core\Controller\Plugin\EntitySnapshot;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntitySnapshotFactory implements FactoryInterface
{
	/**
	 * @param ContainerInterface $container
	 * @param string $requestedName
	 * @param array|null $options
	 *
	 * @return EntitySnapshot
	 */
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$entitySnapshotPlugin = new EntitySnapshot();
		// @TODO actually we just need...
		// an access to all options defining an Snapshot-Generator
		// the Hydrator-Manager
		$entitySnapshotPlugin->setServiceLocator($container);
		$repositories = $container->get('repositories');
		$entitySnapshotPlugin->setRepositories($repositories);
		return $entitySnapshotPlugin;
	}
}
