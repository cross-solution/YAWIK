<?php
/**
 *
 */

namespace Settings\Repository\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Settings\Repository\Settings;

class SettingsFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$settings = new Settings();
		$settings->setServiceLocator($container);
		$settings->setUserRepository($container->get('RepositoryManager')->get('User'));
		$config = $container->get('Config');
		
		// put on the Listener for saving the entity
		$application = $container->get('Application');
		$events = $application->getEventManager();
		$events->attach('postDispatch', array($settings, 'onPostDispatch'));
		return $settings;
	}
}
