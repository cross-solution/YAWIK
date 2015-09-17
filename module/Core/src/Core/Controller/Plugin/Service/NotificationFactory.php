<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** NotifcationFactory.php */
namespace Core\Controller\Plugin\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Controller\Plugin\Notification;
use Core\Listener\Events\NotificationEvent;

class NotificationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $flashMessenger = $serviceLocator->get('FlashMessenger');
        $notification   = new Notification($flashMessenger);

        //$sharedListener = $serviceLocator->getServiceLocator()->get('SharedEventManager');
        //$sharedListener->attach('*', NotificationEvent::EVENT_NOTIFICATION_HTML, array($notification,'createOutput') , 1);

        $notificationListener = $serviceLocator->getServiceLocator()->get('Core/Listener/Notification');
        $notification->setListener($notificationListener);

        return $notification;
    }
}
