<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MailManagerConfig.php */
namespace Core\Mail;

use Zend\Config\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Mail\AddressList;

class MailServiceConfig extends Config
{
    public function getTransport()
    {
        return isset($this->config['transport'])
            ? $this->config['transport']
            : new \Zend\Mail\Transport\Sendmail();
    }
    
    public function getFrom()
    {
        if (isset($this->config['from'])) {
            if (is_string($this->config['from'])) {
                return $this->config['from'];
            }
            if (!is_array($this->config['from']) || !isset($this->config['from']['email'])) {
                return null;
            }
            if (isset($this->config['from']['name'])) {
                return array($this->config['from']['email'] => $this->config['from']['name']);
            }
            return $this->config['from']['email'];
        }
        return null;
    }

    public function getMailer()
    {
        return isset($this->data['mailer'])
            ? $this->data['mailer']
            : 'php/YAWIK';
    }
    
    public function getOverrideRecipient()
    {
        if (!isset($this->data['develop']['override_recipient'])
            || '' == trim($this->data['develop']['override_recipient'])
        ) {
            return false;
        }
        $recipientsStr = $this->data['develop']['override_recipient'];
        $recipientsArr = false !== strpos($recipientsStr, ',')
                       ? explode(',', $recipientsStr)
                       : array($recipientsStr);
        
        $recipientsArr = array_map('trim', $recipientsArr);
        $recipients    = new AddressList();
        foreach ($recipientsArr as $recipient) {
            if (preg_match('~^([^<]+)(?:<([^>]+)>)?$~', $recipient, $match)) {
                if (isset($match[2])) {
                    $recipients->add($match[2], $match[1]);
                } else {
                    $recipients->add($match[1]);
                }
            } else {
                trigger_error('invalid address format ("' . $recipient . '") in mails.develop.override_recipient', E_USER_WARNING);
            }
        }
        return $recipients;
    }
    
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        if (!$serviceManager instanceof MailService) {
            throw new \DomainException('Can only configure instances of \Core\Mail\MailService.');
        }
        
        //parent::configureServiceManager($serviceManager);
        
        $serviceManager->setTransport($this->getTransport());
        $serviceManager->setFrom($this->getFrom());
        $serviceManager->setMailer($this->getMailer());
        
        /*
         * Development configuration
         */
        if ($recipients = $this->getOverrideRecipient()) {
            $serviceManager->setOverrideRecipient($recipients);
        }
        
    }
}
