<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Jobs\Listener;

use Jobs\Entity\Job;
use Jobs\Listener\Events\JobEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Sends mails on job events.
 *
 * @since  0.19
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class MailSender implements ListenerAggregateInterface
{
    /**
     * Attached listeners
     *
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners;

    /**
     * The mail service
     *
     * @var \Core\Mail\MailService
     */
    protected $mailer;

    /**
     * Options array
     *
     * @var array
     */
    protected $options = array(
        'siteName'   => '',
        'adminEmail' => '',
    );

    /**
     * Creates an instance.
     *
     * @param \Core\Mail\MailService $mailService
     * @param array                  $options
     */
    public function __construct($mailService, array $options)
    {
        $this->mailer  = $mailService;
        $this->options = array_merge($this->options, $options);
    }

    public function attach(EventManagerInterface $events, $priority=1)
    {
        $this->listeners[] = $events->attach(JobEvent::EVENT_JOB_CREATED, array($this, 'onJobCreated'));
        $this->listeners[] = $events->attach(JobEvent::EVENT_JOB_ACCEPTED, array($this, 'onJobAccepted'));
        $this->listeners[] = $events->attach(JobEvent::EVENT_JOB_REJECTED, array($this, 'onJobRejected'));
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Callback for the job created event.
     *
     * @param JobEvent $e
     */
    public function onJobCreated(JobEvent $e)
    {
        $job = $e->getJobEntity();
        $this->sendMail(
            $job,
            'mail/job-created',
            /*@translate*/ 'A new job opening was created',
            /*adminMail*/ true
        );
        $this->sendMail(
            $job,
            'mail/job-pending',
            /*@translate*/ 'Your Job have been wrapped up for approval'
        );
    }

    /**
     * Callback for the job accepted event
     *
     * @param JobEvent $e
     */
    public function onJobAccepted(JobEvent $e)
    {
        $this->sendMail(
            $e->getJobEntity(),
            'mail/job-accepted',
            /*@translate*/ 'Your job has been published'
        );
    }

    /**
     * Callback for the job rejected event
     *
     * @param JobEvent $e
     */
    public function onJobRejected(JobEvent $e)
    {
        $this->sendMail(
            $e->getJobEntity(),
            'mail/job-rejected',
            /*@translate*/ 'Your job has been rejected'
        );
    }

    /**
     * Sends a job event related mail
     *
     * @param Job    $job
     * @param string $template
     * @param string $subject
     * @param bool   $adminMail if true, the mail is send to the administrator instead of to the user.
     */
    protected function sendMail(Job $job, $template, $subject, $adminMail = false)
    {
        $mail = $this->mailer->get('htmltemplate');
        $mail->setTemplate($template)
             ->setSubject($subject)
             ->setVariables(
                 array(
                                'job'      => $job,
                                'siteName' => $this->options['siteName'],
                            )
             );

        if ($adminMail) {
            $mail->setTo($this->options['adminEmail']);
        } else {
            if (! ($user = $job->getUser())) {
                return;
            }
            $userInfo  = $user->getInfo();
            $userEmail = $userInfo->getEmail();
            $userName  = $userInfo->getDisplayName(/*emailIfEmpty*/ false);

            $mail->setTo($userEmail, $userName);
        }

        $this->mailer->send($mail);
    }
}
