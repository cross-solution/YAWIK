<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Factory\Listener;

use Auth\Listener\SendRegistrationNotifications;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface; 

/**
 * Factory for \Auth\Listener\SendRegistrationNotifications
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @since 0.30
 */
class SendRegistrationNotificationsFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mailer = $container->get('Core/MailService');
        $options = $container->get('Auth/Options');

        return new SendRegistrationNotifications($mailer, $options);
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, SendRegistrationNotifications::class);
    }
}
