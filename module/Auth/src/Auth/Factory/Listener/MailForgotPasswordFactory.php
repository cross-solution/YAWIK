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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class MailForgotPasswordFactory implements FactoryInterface
{
    /**
     * Creates an instance of MailForgotPassword
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\View\Helper\Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('Auth\Options');
        $coreOptions = $serviceLocator->get('Core\Options');
        $mailService = $serviceLocator->get('Core\MailService');
        $listener = new MailForgotPassword($options,$mailService, $coreOptions);
        return $listener;
    }
}
