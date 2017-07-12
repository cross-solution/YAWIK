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
 * Status Change Listener is called by the event \Applications\Listener\Events\ApplicationEvent::EVENT_APPLICATION_STATUS_CHANGE
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
    	$target = $e->getTarget();
        if ($target->isPostRequest()) {
            return;
        }
	    
        $this->application = $target->getApplicationEntity();
        $status = $target->getStatus();
        $user = $target->getUser();
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
            'to'            => $this->getRecipient($this->application, $status),
        );
        $target->setFormData($data);
    }

    /**
     * Sends the Notification Mail.
     *
     * @param ApplicationEvent $event
     */
    public function sendMail(ApplicationEvent $event){
    	$event = $event->getTarget();
        if (!$event->isPostRequest()) {
            return;
        }

        $this->application = $event->getApplicationEntity();
        $status = $event->getStatus();
        $user = $event->getUser();
        $post = $event->getPostData();

        $settings = $user->getSettings('Applications');
        $recipient = $this->getRecipient($this->application, $status);
        /* @var \Applications\Mail\StatusChange $mail */
        $mail = $this->mailService->get('Applications/StatusChange');

        $mail->setSubject($post['mailSubject']);
        $mail->setBody($post['mailText']);
        $mail->setTo($recipient);

        if ($from = $this->application->getJob()->getContactEmail()) {
            $mail->setFrom($from, $this->application->getJob()->getCompany());
        }

        if ($settings->mailBCC) {
            $mail->addBcc($user->getInfo()->getEmail(), $user->getInfo()->getDisplayName());
        }
        $job = $this->application->getJob();
        $jobUser = $job->getUser();
        if ($jobUser->getId() != $user->getId()) {
            $jobUserSettings = $jobUser->getSettings('Applications');
            if ($jobUserSettings->getMailBCC()) {
                $mail->addBcc($jobUser->getInfo()->getEmail(), $jobUser->getInfo()->getDisplayName(false));
            }
        }

        $org = $job->getOrganization()->getParent(/*returnSelf*/ true);
        $orgUser = $org->getUser();
        if ($orgUser->getId() != $user->getId() && $orgUser->getId() != $jobUser->getId()) {
            $orgUserSettings = $orgUser->getSettings('Applications');
            if ($orgUserSettings->getMailBCC()) {
                $mail->addBcc($orgUser->getInfo()->getEmail(), $orgUser->getInfo()->getDisplayName(false));
            }
        }
        $this->mailService->send($mail);


        $historyText = sprintf($this->translator->translate('Mail was sent to %s'), key($recipient) ?: $recipient[0] );
        $this->application->changeStatus($status, $historyText);
        $event->setNotification($historyText);
    }

    /**
     * @param Application $application
     * @param             $status
     *
     * @return AddressList
     */
    protected function getRecipient( Application $application, $status) {

        $recipient = Status::ACCEPTED == $status
            ? $application->getJob()->getUser()->getInfo()
            : $application->getContact();

        $email = $recipient->getEmail();
        $name  = $recipient->getDisplayName(false);

        return $name ? [ $email => $name ] : [ $email ];
    }

}