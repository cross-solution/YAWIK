<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MailServiceFactory.php */ 
namespace Core\Mail;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailServiceFactory implements FactoryInterface
{
     
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $configArray = $serviceLocator->get('Config');
        $configArray = isset($configArray['mails']) ? $configArray['mails'] : array();
        $config      = new MailServiceConfig($configArray);
        
        $service   = new MailService($config);
        
        return $service;
        
    }

}

