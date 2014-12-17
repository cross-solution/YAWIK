<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Listener;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Jobs\Listener\Events\JobEvent;

/**
 * Job listener for triggering actions like sending mail notification
 *
 * @package Jobs\Listener
 */
class PortalListener implements ServiceManagerAwareInterface
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
    public function __invoke(JobEvent $e)
    {
        $serviceManager = $this->getServiceManager();

        /**
         * the sender of the mail is the currently logged in user
         */
        $authService    = $serviceManager->get('authenticationservice');
        $userEmail      = $authService->getUser()->info->email;
        $userName       = $authService->getUser()->info->displayName;

        /**
         * the receiver of the mail is the "admin" of the running yawik installation
         */
        $config         = $serviceManager->get('config');
        $email          = $config['Auth']['default_user']['email'];
        $job            = $e->getJobEntity();
        $mailService    = $serviceManager->get('Core/MailService');
        $mail           = $mailService->get('htmltemplate');
        $mail->setTemplate('mail/jobCreatedMail');
        $mail->setSubject( /*translate*/ 'A New Job was created');
        $mail->setTo($email);

        /**
         * look above $userName and $userEmail
         */
        $mail->setFrom($userEmail, $userName);
        $mail->job = $job;
        $mailService->send($mail);
        return;

    }

}