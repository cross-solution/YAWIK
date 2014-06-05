<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** NotifcationFactory.php */ 
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
        
        return $notification;
    }
}

