<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MailServiceFactory.php */
namespace Core\Mail;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Smtp;

class MailServiceFactory implements FactoryInterface
{
     
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $mails = isset($config['mails']) ? $config['mails'] : [];

        /* @var \Auth\Options\ModuleOptions $authOptions */
        $authOptions = $serviceLocator->get('Auth/Options');

        /* @var \Core\Options\MailServiceOptions $mailServiceOptions */
        $mailServiceOptions = $serviceLocator->get('Core/MailServiceOptions');

        $configArray = [
                'from' => [
                    'name' => $authOptions->getFromName(),
                    'email' => $authOptions->getFromEmail()
                ],
        ];

        if ($mailServiceOptions->getTransportClass() == 'smtp') {
            $configArray['transport'] = new Smtp($mailServiceOptions);
        }

        $configArray = array_merge($configArray, $mails);

        $config = new MailServiceConfig($configArray);

        $service   = new MailService($serviceLocator, $config);

        return $service;

    }
}
