<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mvc\MvcEvent;

class Mail extends AbstractPlugin 
{
    protected $mails;
    
    public function setController(Dispatchable $controller) {
        $issetController = $this->getController();
        if (!isset($issetController)) {
            $events = $controller->getEventManager();
            $events->attach(MvcEvent::EVENT_RENDER,array($this,'sendMail'), 100);
            parent::setController($controller); 
        }
    }
    
    public function sendMail(MvcEvent $e) {
    }
}
