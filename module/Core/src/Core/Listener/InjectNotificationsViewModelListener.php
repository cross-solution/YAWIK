<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** InjectNotificationsViewModelListener.php */ 
namespace Core\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class InjectNotificationsViewModelListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events) 
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRender'), -100);
    }
    
    public function onRender(MvcEvent $event)
    {
        $layout = $event->getViewModel();
        if ($layout->terminate()) {
            return;
        }
        
        $notificationsModel = new ViewModel();
        $notificationsModel->setTemplate('core/notifications');
        $layout->addChild($notificationsModel, 'notifications');
    }

}

