<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        /* @var \Auth\Options\ModuleOptions $authOptions */
        $authOptions = $serviceLocator->get('Auth\Options');
        $configArray=[
                'from' => [
                    'name' => $authOptions->getFromName(),
                    'email' => $authOptions->getFromEmail()
                ]
        ];

        $config      = new MailServiceConfig($configArray);
        
        $service   = new MailService($config);
        
        return $service;
        
    }
}
