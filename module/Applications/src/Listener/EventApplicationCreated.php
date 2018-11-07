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

use Applications\Entity\Settings;
use Applications\Entity\StatusInterface;
use Applications\Entity\Application;
use Applications\Mail\Confirmation;
use Core\Mail\MailService;
use Applications\Listener\Events\ApplicationEvent;
use Applications\Options\ModuleOptions;
use Organizations\Entity\EmployeeInterface;

/**
 * This Listener sends mails to various users if a new application is created.
 *
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
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
     *
     * @param MailService   $mailService
     */
    public function __construct(MailService $mailService)
    {
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
        /* @var Settings $adminSettings */
        $job = $this->application->getJob();
        $org = $job->getOrganization()->getParent(/*returnSelf*/ true);
        $workflow = $org->getWorkflowSettings();
        $admin = $org->getUser();
        $adminSettings = $admin->getSettings('Applications');
        $mailBcc = $adminSettings->getMailBCC();

        if ($workflow->getAcceptApplicationByDepartmentManager()) {
            /* Send mail to department manager, if there is at least one. */
            $assignedManagers = $job->getMetaData('organizations:managers', []);
            if (count($assignedManagers)) {
                $managers = [];
                foreach ($assignedManagers as $manager) {
                    $manager = $org->getEmployee($manager['id']);
                    if ($manager) {
                        $managers[] = $manager;
                    }
                }
            } else {
                $managers = $org->getEmployeesByRole(EmployeeInterface::ROLE_DEPARTMENT_MANAGER);
            }

            if (count($managers)) {
                foreach ($managers as $employee) {
                    /* @var EmployeeInterface $employee */
                    $this->mailService->send(
                        'Applications/NewApplication',
                        [
                            'application' => $this->application,
                            'user' => $employee->getUser(),
                            'bcc' => $adminSettings->getMailBCC() ? [ $admin ] : null,
                        ]
                    );
                }
                return;
            }
        }


        $recruiter = $job->getUser();
        /* @var \Applications\Entity\Settings $settings */
        $settings = $recruiter->getSettings('Applications');
        if ($settings->getMailAccess()) {
            $this->mailService->send(
                'Applications/NewApplication',
                [
                    'job'   => $this->application->getJob(),
                    'user'  => $recruiter,
                    'bcc' => $adminSettings->getMailBCC() ? [ $admin ] : null,
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
                    Confirmation::class,
                    [
                        'application' => $this->application,
                        'body'        => $ackBody,
                        'bcc'         => $adminSettings->getMailBCC() ? [ $admin ] : null,
                    ]
                );

                // Must be called after initializers in creation
                $ackMail->setSubject(/* @translate */ 'Application confirmation');

                $ackMail->setFrom($recruiter->getInfo()->getEmail(), $recruiter->getInfo()->getDisplayName(false));
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
}
