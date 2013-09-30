<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Mvc\MvcEvent;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Core\Mail\Mail;

class Mailer extends AbstractPlugin 
{
    protected $mails;
    
    public function setController(Dispatchable $controller) {
        $issetController = $this->getController();
        if (!isset($issetController)) {
            $events = $controller->getEventManager();
            $events->attach(MvcEvent::EVENT_RENDER, array($this,'renderMail'), 100);
            parent::setController($controller); 
        }
    }
    
    public function renderMail(MvcEvent $e) {
    }
    
    public function sendMail($mail) {
        $result = False;
        if (($id = array_search($mail, $this->mails)) !== False) {
            $transport = new Sendmail();
            $transport->send($mail);
            
            // unsetting the mail reassures us, that we will not send the mail more than once
            unset ($this->mails[$id]);
            $result = True;
        }
        return $result;
    }
    
    public function newMail() {
        $mail = new Mail($this);
        $this->mails[] = $mail;
        return $mail;
    }
}
