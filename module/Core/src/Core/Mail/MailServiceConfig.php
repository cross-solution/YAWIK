<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** MailManagerConfig.php */ 
namespace Core\Mail;

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

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
        return isset($this->config['from'])
            ? $this->config['from']
            : null;
    }

    public function getMailer()
    {
        return isset($this->config['mailer'])
            ? $this->config['mailer']
            : 'php/CrossApplicantManagement';
    }
    
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        if (!$serviceManager instanceOf MailService) {
            throw new \DomainException('Can only configure instances of \Core\Mail\MailService.');
        }
        
        parent::configureServiceManager($serviceManager);
        
        $serviceManager->setTransport($this->getTransport());
        $serviceManager->setFrom($this->getFrom());
        $serviceManager->setMailer($this->getMailer());
    }


}

