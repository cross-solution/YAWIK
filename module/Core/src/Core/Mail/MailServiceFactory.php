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

use Core\Options\MailServiceOptions;
use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Transport\Sendmail;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Mail\Transport\Smtp;

/**
 * Class MailServiceFactory
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Core\Mail
 */
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

		$configArray['transport'] = $this->getTransport($mailServiceOptions);
		$configArray = array_merge($configArray, $mails);
		
		$config = new MailServiceConfig($configArray);
		$service   = new MailService($container,$config->toArray());
		$config->configureServiceManager($service);
		foreach($config->toArray() as $name=>$value){
			$method = 'set'.$name;
			if(method_exists($service,$method)){
				call_user_func([$service,$method],$value);
			}
		}
		
		return $service;
	}

	public function getTransport(MailServiceOptions $mailServiceOptions)
    {
        $type = $mailServiceOptions->getTransportClass();
        if (MailService::TRANSPORT_SMTP == $type) {
            return new Smtp($mailServiceOptions);
        }elseif(MailService::TRANSPORT_FILE == $type){
            $fileOptions = new FileOptions();
            $fileOptions->setPath($mailServiceOptions->getPath());
            return new FileTransport($fileOptions);
        }elseif(MailService::TRANSPORT_SENDMAIL == $type){
            return new Sendmail();
        }

        throw new ServiceNotCreatedException(
            sprintf(
                '"%s" is not a valid email transport type. Please use smtp or file as email transport',
                $type
            )
        );
    }
}
