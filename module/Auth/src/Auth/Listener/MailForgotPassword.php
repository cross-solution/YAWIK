<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Listener;

use Auth\AuthenticationService;
use Zend\Mvc\Service\ControllerPluginManagerFactory;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Auth\Listener\Events\AuthEvent;

/**
 *
 *
 * @package Auth\Listener
 */
class MailForgotPassword implements ServiceManagerAwareInterface
{
    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    /**
     * allows an event attachment just by class
     * @param JobEvent $e
     */
    public function __invoke(AuthEvent $e)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager          = $this->getServiceManager();

        /**
         * the sender of the mail is the currently logged in user
         */
        /** @var AuthenticationService $authService */
        $authService             = $serviceManager->get('authenticationservice');
        $user                    = $e->getUser();
        $userEmail               = $user->info->email;
        $userName                = $user->info->displayName;
        $resetLink               = $e->getResetLink();

        /** @var ControllerPluginManagerFactory $controllerPluginManager */
        //$controllerPluginManager = $serviceManager->get('controllerPluginManager');
        //$urlPlugin               = $controllerPluginManager->get('url');

        /**
         * the receiver of the mail is the "admin" of the running yawik installation
         */
        //$config                  = $serviceManager->get('config');
        $mailService             = $serviceManager->get('Core/MailService');
        $mail                    = $mailService->get('htmltemplate');
        $mail->user              = $user;
        $mail->resetlink         = $resetLink;
        $mail->setTemplate('mail/forgotPassword');
        $mail->setSubject( /*translate*/ 'new Password');
        $mail->setTo($userEmail);
        $mail->setFrom('Yawik-System', $userName);
        $mailService->send($mail);
        return;

    }
}