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
    protected $mailService;
    
    public function setMailService($mailService)
    {
        $this->mailService = $mailService;
        return $this;
    }
    
    public function getMailService()
    {
        if (!$this->mailService) {
            $services    = $this->getController()->getServiceLocator();
            $mailService = $services->get('Core/MailService');
            $this->setMailService($mailService); 
        }
        return $this->mailService;
    }
    
    public function __invoke($method=null)
    {
        $mailService = $this->getMailService();
        if (null !== $method && method_exists($mailService, $method)) {
            $params = func_get_args();
            array_shift($params); // Discard first param ($method)
            
            return call_user_func_array(array($mailService, $method), $params);
        }
        
        return $mailService;
    }
}
