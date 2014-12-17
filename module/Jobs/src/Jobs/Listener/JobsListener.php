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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Jobs\Listener\Events\JobEvent;

/**
 * Aggregate for all Action concerning Jobs in the Job-Module
 * most handlers are here
 * more sophisticated handlers or handlers that involve similar tasks, are pooled in an own class (like Portals)
 *
 * @package Jobs\Listener
 */
class JobsListener implements ListenerAggregateInterface, ServiceManagerAwareInterface
{

    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function attach(EventManagerInterface $events)
    {
        //$eventsApplication = $this->getServiceManager()->get("Application")->getEventManager();

        //$events->attach(JobEvent::EVENT_NEW, array($this, 'jobNewMail'), 1);
        $events->attach(JobEvent::EVENT_SEND_PORTALS, $this->getServiceManager()->get('Jobs/PortalListener') , 1);

        return $this;
    }

    public function detach(EventManagerInterface $events)
    {
        return $this;
    }

    /**
     * Sends a notification mail about a created job position
     *
     * can this be deleted?
     *
     * @param JobEvent $e
     */
    public function jobNewMail(JobEvent $e) {
        $serviceManager = $this->getServiceManager();
        $config = $serviceManager->get('config');
        // @TODO check with isset to avoid an exception
        $email = $config['Auth']['default_user']['email'];
        $job = $e->getJobEntity();
        $mailService = $this->getServiceManager()->get('Core/MailService');
        $mail = $mailService->get('stringtemplate');
        $mail->setSubject('Subject');
        $mail->setBody('body ' . $job->id . ', Title: ' . $job->title);
        $mail->setTo($email);
        $mail->setFrom('from', 'fromName');
        $mail->addBcc('Adresse', 'displayName');
        $mailService->send($mail);

    }
}
