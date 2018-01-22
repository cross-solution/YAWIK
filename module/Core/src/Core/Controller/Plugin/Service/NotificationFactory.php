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

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Controller\Plugin\Notification;

/**
 * Create Notification plugin
 *
 * @package Core\Controller\Plugin\Service
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class NotificationFactory implements FactoryInterface
{
    /**
     * Create new Notification object
     *
     * @param ContainerInterface    $container
     * @param string                $requestedName
     * @param array|null            $options
     * @return Notification
     */
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
}
