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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Controller\Plugin\Notification;

class NotificationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $flashMessenger = $serviceLocator->get('FlashMessenger');
        $notification   = new Notification($flashMessenger);

        $notificationListener = $serviceLocator->getServiceLocator()->get('Core/Listener/Notification');
        $notification->setListener($notificationListener);

        return $notification;
    }
}
