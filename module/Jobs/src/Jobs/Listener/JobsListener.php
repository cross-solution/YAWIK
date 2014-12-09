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

class JobsListener implements ListenerAggregateInterface, ServiceManagerAwareInterface
{
    protected $serviceLocator;

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceLocator = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceLocator;
    }

    public function attach(EventManagerInterface $events)
    {
        $eventsApplication = $this->getServiceManager()->get("Application")->getEventManager();

        $events->attach(JobEvent::EVENT_JOB_NEW, array($this, 'jobNewMail'), 1);

        return $this;
    }

    public function detach(EventManagerInterface $events)
    {
        return $this;
    }

    public function jobNewMail(JobEvent $e) {
        $job = $e->getJobEntity();
        $mailService = $this->getServiceManager()->get('Core/MailService');
        $mail = $mailService->get('stringtemplate');
        $mail->setSubject('Subject');
        $mail->setBody('body ' . $job->id . ', Title: ' . $job->title);
        $mail->setTo('weitz@cross-solution.de');
        $mail->setFrom('from', 'fromName');
        $mail->addBcc('Adresse', 'displayName');
        $mailService->send($mail);

    }

}
