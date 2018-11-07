<?php

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mail\Message;
use Core\Mail\MailService;

class Mailer extends AbstractPlugin
{
    
    /**
     * @var MailService
     */
    protected $mailService;
    
    public function __construct(
        MailService $mailService
    ) {
        $this->mailService = $mailService;
    }
    
    /**
     * @param MailService $mailService
     * @return \Core\Controller\Plugin\Mailer
     */
    public function setMailService(MailService $mailService)
    {
        $this->mailService = $mailService;
        return $this;
    }
    
    /**
     * @return MailService
     */
    public function getMailService()
    {
        return $this->mailService;
    }
    
    public function __call($method, $params)
    {
        $mailService = $this->getMailService();
        $callback    = array($mailService, $method);
        
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        
        throw new \BadMethodCallException(
            sprintf(
                'Neither proxied class "%s" nor this class knows of a method called "%s"',
                get_class($mailService),
                $method
            )
        );
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
        if (null === $mail) {
            return $this;
        }
        if ($mail instanceof Message) {
            return $this->send($mail);
        }
        
        if (is_bool($options)) {
            $sendMail = $options;
            $options  = array();
        }
        
        $mail = $this->get($mail, $options);

        return $sendMail ? $this->send($mail) : $mail;
    }
    
    /**
     * @param ContainerInterface $container
     *
     * @return Mailer
     */
    public static function factory(ContainerInterface $container)
    {
        return new static($container->get('Core/MailService'));
    }
}
