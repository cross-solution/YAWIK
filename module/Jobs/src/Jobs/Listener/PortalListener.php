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

    public function __invoke(JobEvent $e)
    {
        $serviceManager = $this->getServiceManager();
        $config = $serviceManager->get('config');
        // @TODO check with isset to avoid an exception
        $email = $config['Auth']['default_user']['email'];
        $job = $e->getJobEntity();
        $mailService = $this->getServiceManager()->get('Core/MailService');
        $mail = $mailService->get('htmltemplate');
        $mail->setTemplate('mail/portalmail');
        $mail->setSubject('Subject');
        //$mail->setBody('body ' . $job->id . ', Title: ' . $job->title);
        $mail->setTo($email);
        $mail->setFrom('from', 'fromName');
        $mail->addBcc('Adresse', 'displayName');
        $mail->job = $job;
        $mailService->send($mail);
        return;

    }

}