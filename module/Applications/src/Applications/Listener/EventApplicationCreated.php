<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Listener;

use Applications\Entity\StatusInterface;
use Applications\Entity\Application;
use Core\Mail\MailService;
use Applications\Listener\Events\ApplicationEvent;
use Applications\Options\ModuleOptions;
use Zend\Session\Container;

/**
 * ${CARET}
 * 
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @todo write test 
 */
class EventApplicationCreated
{
    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @param ModuleOptions $options
     * @param MailService   $mailService
     */
    public function __construct(ModuleOptions $options,MailService $mailService)
    {
        $this->options = $options;
        $this->mailService = $mailService;
    }

    public function __invoke(ApplicationEvent $event)
    {
        $this->application = $event->getApplicationEntity();
        $this->sendRecruiterMails();
        $this->sendUserMails();
    }

    protected function sendRecruiterMails()
    {

        $recruiter = $this->getRecruiter();
        $job = $this->getJob();

        /* @var \Applications\Entity\Settings $settings */
        $settings = $recruiter->getSettings('Applications');
        if ($settings->getMailAccess()) {
            $this->mailService->get(
                'Applications/NewApplication',
                [
                    'job' => $job,
                    'user' => $recruiter,
                    'admin' => $this->getOrganizationAdmin()
                ],
                /*send*/ true
            );
        }
        if ($settings->getAutoConfirmMail()) {
            $ackBody = $settings->getMailConfirmationText();
            if (empty($ackBody)) {
                $ackBody = $job->user->getSettings('Applications')->getMailConfirmationText();
            }
            if (!empty($ackBody)) {
                /* confirmation mail to the applicant */
                $ackMail = $this->mailService->get(
                    'Applications/Confirmation',
                    [
                        'application' => $this->application,
                        'body' => $ackBody,
                    ]
                );

                // Must be called after initializers in creation
                $ackMail->setSubject( /* @translate */ 'Application confirmation');

                $ackMail->setFrom($recruiter->getInfo()->getEmail());
                $this->mailService->send($ackMail);
                $this->application->changeStatus(StatusInterface::CONFIRMED, sprintf('Mail was sent to %s', $this->application->getContact()->getEmail()));
            }
        }
    }

    protected function sendUserMails()
    {
        if ($this->application->getAttributes()->getSendCarbonCopy()) {
            $this->mailService->get(
                 'Applications/CarbonCopy',
                 [
                     'application' => $this->application
                 ],
                 /*send*/ true
            );
        }
    }

    /**
     * @return \Auth\Entity\UserInterface
     */
    public function getRecruiter(){
        return $this->getJob()->getUser();
    }

    /**
     * @return \Jobs\Entity\JobInterface
     */
    public function getJob(){
        return $this->application->getJob();
    }

    /**
     * @return \Auth\Entity\UserInterface|bool
     */
    public function getOrganizationAdmin(){
        if ($this->getRecruiter()->getOrganization()->isOwner()){
            return $this->getRecruiter();
        }elseif($this->getRecruiter()->getOrganization()->hasAssociation()) {
            return $this->getRecruiter()->getOrganization()->getOrganization()->getUser();
        }else{
            return false;
        }
    }
}