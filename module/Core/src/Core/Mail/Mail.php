<?php

namespace Core\Mail;

use Zend\Mail\Message;

class Mail extends Message
{
    protected $_mailer;
    protected $_emailOrAddressOrList;

    /**
     * @param $mailer
     */
    public function __construct($mailer)
    {
        $this->_mailer = $mailer;
        return $this;
    }
    
    protected function getMailer()
    {
        return $this->_mailer;
    }
    
    public function send()
    {
        return $this->getMailer()->sendMail($this);
    }
}
