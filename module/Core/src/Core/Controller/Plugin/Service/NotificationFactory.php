<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** NotificationFactory.php */
namespace Core\Controller\Plugin\Service;

use Core\Listener\Events\NotificationEvent;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Controller\Plugin\Notification;

class NotificationFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$pluginManager = $container->get('ControllerPluginManager');
		$flashMessenger = $pluginManager->get('FlashMessenger');
		$translator = $container->get('translator');
		
		$notificationListener = $container->get('Core/Listener/Notification');
		$notification   = new Notification($flashMessenger);
		$notification->setListener($notificationListener);
		$notification->setTranslator($translator);
		
		return $notification;
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Zend\Mvc\Controller\PluginManager $serviceLocator */
        return $this($serviceLocator,Notification::class);
    }
}
