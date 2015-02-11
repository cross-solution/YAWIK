<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mail\Message;

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
    
    public function __call($method, $params)
    {
        $mailService = $this->getMailService();
        $callback    = array($mailService, $method);
        
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        
        throw new \BadMethodCallException(sprintf(
            'Neither proxied class "%s" nor this class knows of a method called "%s"',
            get_class($mailService), $method
        ));
    }
    
    public function get($mailPluginName, array $options = array())
    {
        return $this->getMailService()->get($mailPluginName, $options);
    }
    
    public function send(Message $mail)
    {
        return $this->getMailService()->send($mail);
    }
    
    public function __invoke($mail = null, $options = array(), $sendMail = false)
    {
        if (null === $mail)  {
            return $this;
        }
        if ($mail instanceOf Message) {
            return $this->send($mail);
        }
        
        if (is_bool($options)) {
            $sendMail = $options;
            $options  = array();
        }
        
        $mail = $this->get($mail, $options);

        return $sendMail ? $this->send($mail) : $mail;
    }
}
