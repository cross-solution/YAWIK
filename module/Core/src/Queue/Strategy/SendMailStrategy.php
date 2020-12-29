<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Core\Queue\Strategy;

use Core\Mail\MailService;
use Core\Queue\Job\ExceptionJobResult;
use Core\Queue\Job\JobResult;
use Core\Queue\Job\MailSenderInterface;
use Core\Queue\Job\ResultProviderInterface;
use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class SendMailStrategy extends AbstractStrategy
{
    private $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Registers itself with an EventManager
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1) : void
    {
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'sendMail'], -900);
    }

    public function sendMail(ProcessJobEvent $event)
    {
        $job = $event->getJob();

        if (!$job instanceof MailSenderInterface) {
            $event->setResult(ProcessJobEvent::JOB_STATUS_FAILURE);
            if ($job instanceof ResultProviderInterface) {
                $job->setResult(JobResult::failure('This queue can only consume Jobs which implement the ' . MailSenderInterface::class));
            }
        }

        try {
            $this->mailService->send($job->getMail());
        } catch (\Throwable $e) {

            $event->setResult(ProcessJobEvent::JOB_STATUS_FAILURE);
            if ($job instanceof ResultProviderInterface) {
                $job->setResult(new ExceptionJobResult($e));
            }
            return;
        }

        $event->setResult(ProcessJobEvent::JOB_STATUS_SUCCESS);
        if ($job instanceof ResultProviderInterface) {
            $job->setResult(JobResult::success('Mail send successfully.'));
        }
    }
}
