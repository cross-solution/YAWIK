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

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Smtp;

class MailServiceFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$config = $container->get('Config');
		$mails = isset($config['mails']) ? $config['mails'] : [];
		
		/* @var \Auth\Options\ModuleOptions $authOptions */
		$authOptions = $container->get('Auth/Options');
		
		/* @var \Core\Options\MailServiceOptions $mailServiceOptions */
		$mailServiceOptions = $container->get('Core/MailServiceOptions');
		
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
		
		$service   = new MailService($container, $config->toArray());
		
		return $service;
	}
}
