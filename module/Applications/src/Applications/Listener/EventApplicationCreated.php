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
     * The mail service
     *
     * @var \Core\Mail\MailService
     */
    protected $mailService;

    /**
     * @param ModuleOptions $options
     * @param MailService   $mailService
     */
    public function __construct(ModuleOptions $options, MailService $mailService)
    {
        $this->options     = $options;
        $this->mailService = $mailService;
    }

    public function __invoke(ApplicationEvent $event)
    {
        $this->application = $event->getApplicationEntity();
        $this->sendRecruiterMails();
        $this->sendCarbonCopyToCandidate();
    }

    protected function sendRecruiterMails()
    {
        $recruiter = $this->application->getJob()->getUser();

        /* @var \Applications\Entity\Settings $settings */
        $settings = $recruiter->getSettings('Applications');
        if ($settings->getMailAccess()) {
            $this->mailService->send(
                'Applications/NewApplication',
                [
                    'job'   => $this->application->getJob(),
                    'user'  => $recruiter,
                    'admin' => $this->getOrganizationAdmin()
                ]
            );
        }
        if ($settings->getAutoConfirmMail()) {
            $ackBody = $settings->getMailConfirmationText();
            if (empty($ackBody)) {
                $ackBody = $settings->getMailConfirmationText();
            }
            if (!empty($ackBody)) {
                /* confirmation mail to the applicant */
                $ackMail = $this->mailService->get(
                    'Applications/Confirmation',
                    [
                        'application' => $this->application,
                        'body'        => $ackBody,
                    ]
                );

                // Must be called after initializers in creation
                $ackMail->setSubject(/* @translate */ 'Application confirmation' );

                $ackMail->setFrom($recruiter->getInfo()->getEmail());
                $this->mailService->send($ackMail);
                $this->application->changeStatus(
                    StatusInterface::CONFIRMED,
                    sprintf('Mail was sent to %s', $this->application->getContact()->getEmail())
                );
            }
        }
    }

    /**
     * Send Carbon Copy to the User
     */
    protected function sendCarbonCopyToCandidate()
    {
        if ($this->application->getAttributes()->getSendCarbonCopy()) {
            $this->mailService->send(
                'Applications/CarbonCopy',
                [
                    'application' => $this->application
                ]
            );
        }
    }

    /**
     * @return \Auth\Entity\UserInterface|bool
     */
    protected function getOrganizationAdmin()
    {
        $recruiter = $this->application->getJob()->getUser();
        if ($recruiter->getOrganization()->isOwner()) {
            return true;
        } elseif ($recruiter->getOrganization()->hasAssociation()) {
            return $recruiter->getOrganization()->getOrganization()->getUser();
        } else {
            return false;
        }
    }
}