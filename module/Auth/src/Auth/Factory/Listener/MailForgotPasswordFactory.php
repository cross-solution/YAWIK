<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Listener;

use Auth\Listener\MailForgotPassword;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class MailForgotPasswordFactory implements FactoryInterface
{
    /**
     * Create a MailForgotPassword Listener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return MailForgotPassword
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('Auth/Options');
        $coreOptions = $container->get('Core/Options');
        $mailService = $container->get('Core/MailService');
        $listener = new MailForgotPassword($options, $mailService, $coreOptions);
        return $listener;
    }
    /**
     * Creates an instance of MailForgotPassword
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\View\Helper\Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, MailForgotPassword::class);
    }
}
