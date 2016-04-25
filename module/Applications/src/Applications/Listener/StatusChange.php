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

use Applications\Entity\Application;
use Applications\Entity\Status;
use Core\Mail\MailService;
use Applications\Listener\Events\ApplicationEvent;
use Applications\Options\ModuleOptions;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeeInterface;
use Organizations\Entity\WorkflowSettings;
use Zend\Mail\AddressList;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;

/**
 * Status Change Listener is called twice. Triggered by
 *
 * ${CARET}
 * 
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @todo write test 
 */
class StatusChange
{
    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @var MailService $mailService
     */
    protected $mailService;

    /**
     * @var Translator $translator
     */
    protected $translator;

    /**
     * @param ModuleOptions $options
     * @param MailService   $mailService
     * @param Translator    $translator
     */
    public function __construct(
        ModuleOptions $options,
        MailService $mailService,
        Translator $translator)
    {
        $this->options = $options;
        $this->mailService = $mailService;
        $this->translator = $translator;
    }


    /**
     * Prepares the Notification Mail
     *
     * @param ApplicationEvent $e
     */
    public function prepareFormData(ApplicationEvent $e){
        if ($e->isPostRequest()) {
            return;
        }

        $this->application = $e->getApplicationEntity();
        $status = $e->getStatus();
        $user = $e->getUser();
        $settings = $user->getSettings('Applications');

        /* @var \Applications\Mail\StatusChange $mail */
        $mail = $this->mailService->get('Applications/StatusChange');

        switch ($status) {
            case Status::CONFIRMED:
                $key = 'mailConfirmationText';
                break;
            case Status::INVITED:
                $key = 'mailInvitationText';
                break;
            case Status::ACCEPTED:
                $key = 'mailAcceptedText';
                break;
            case Status::REJECTED:
                $key = 'mailRejectionText';
                break;
            default:
                throw new \InvalidArgumentException('Unknown status value: ' .$status);
        }
        $mailText      = $settings->$key ? $settings->$key : '';
        $mail->setBody($mailText);
        $mail->setApplication($this->application);
        $mailText = $mail->getBodyText();
        $mailSubject   = sprintf(
            $this->translator->translate('Your application dated %s'),
            strftime('%x', $this->application->getDateCreated()->getTimestamp())
        );

        $data = array(
            'applicationId' => $this->application->getId(),
            'status'        => $status,
            'mailSubject'   => $mailSubject,
            'mailText'      => $mailText,
            'to'            => $this->getRecipients($this->application, $status),
        );
        $e->setFormData($data);
    }

    /**
     * Sends the Notification Mail.
     *
     * @param ApplicationEvent $event
     */
    public function  sendMail(ApplicationEvent $event){
        if (!$event->isPostRequest()) {
            return;
        }

        $this->application = $event->getApplicationEntity();
        $status = $event->getStatus();
        $user = $event->getUser();
        $post = $event->getPostData();

        $settings = $user->getSettings('Applications');

        /* @var \Applications\Mail\StatusChange $mail */
        $mail = $this->mailService->get('Applications/StatusChange');

        $mail->setSubject($post['mailSubject']);
        $mail->setBody($post['mailText']);
        $mail->setTo($this->getRecipients($this->application,$status));

        if ($from = $this->application->getJob()->getContactEmail()) {
            $mail->setFrom($from, $this->application->getJob()->getCompany());
        }

        if ($settings->mailBCC) {
            $mail->addBcc($user->getInfo()->getEmail(), $user->getInfo()->getDisplayName());
        }
        $this->mailService->send($mail);


        $historyText = sprintf($this->translator->translate('Mail was sent to %s'), $this->application->getContact()->getEmail());
        $this->application->changeStatus($status, $historyText);
        $event->setNotification($historyText);
    }

    /**
     * @param Application $application
     * @param             $status
     *
     * @return AddressList
     */
    protected function getRecipients( Application $application, $status) {

        $job = $application->getJob();
        $organization = $job->getOrganization();

        $to = $cc = $bcc = new AddressList();

        switch($status) {
            case Status::INCOMING:
                /* @var WorkflowSettings $workflow */
                $workflow = $job->getOrganization()->getWorkflowSettings();
                if ($workflow->getAcceptApplicationByDepartmentManager()) {
                    $departmentManagers = $job->getOrganization()->getEmployeesByRole(EmployeeInterface::ROLE_DEPARTMENT_MANAGER);
                    foreach($departmentManagers as $employee ) { /* @var Employee $employee */
                        $to->add(
                            $employee->getUser()->getInfo()->getEmail(),
                            $employee->getUser()->getInfo()->getDisplayName(false)
                        );
                    }
                    if (empty($to)) {
                        $to->add(
                            $job->getUser()->getInfo(),
                            $job->getUser()->getInfo()->getDisplayName(false)
                        );
                    }
                }
                break;
            case Status::CONFIRMED:
                $to->add(
                    $application->getContact()->getEmail(),
                    $application->getContact()->getDisplayName(false)
                    );
                break;
            case Status::INVITED:
                $to->add(
                    $application->getContact()->getEmail(),
                    $application->getContact()->getDisplayName(false)

                );
                break;
            case Status::ACCEPTED:
                $to->add(
                    $job->getUser()->getInfo()->getEmail(),
                    $job->getUser()->getInfo()->getDisplayName(false)
                );
                break;
            case Status::REJECTED:
                $to->add(
                    $application->getContact()->getEmail(),
                    $application->getContact()->getDisplayName(false)
                );
                break;
            default:
                throw new \InvalidArgumentException('Unknown status value: ' .$status);
                break;
        }

        /* @var \Applications\Entity\Settings $organizationAdminSettings */
        $organizationAdminSettings = $organization->getUser()->getSettings('Applications');
        if ($organizationAdminSettings->mailBCC) {
            $bcc->add(
                $organization->getUser()->getInfo()->getEmail(),
                $organization->getUser()->getInfo()->getDisplayName(false)
            );
        }

        return $to;
    }

}