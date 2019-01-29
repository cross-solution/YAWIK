<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Strategy;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\FinishEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class LogStrategy extends AbstractStrategy
{
    private $logger;
    private $tmpl = [
        'queue' => '%s queue: %s',
        'job'   => '{ %s } [ %s ] %s%s',
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        if (!$this->logger) { return; }

        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_BOOTSTRAP, [$this, 'logBootstrap']);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_FINISH, [$this, 'logFinish']);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobStart'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobEnd'], -1000);
    }

    public function logBootstrap(BootstrapEvent $event)
    {
        $this->logger->info(sprintf(
            $this->tmpl['queue'],
            'Start',
            $event->getQueue()->getName()
        ));
    }

    public function logFinish(FinishEvent $event)
    {
        $this->logger->info(sprintf(
            $this->tmpl['queue'],
            'Stop',
            $event->getQueue()->getName()
        ));
    }

    public function logJobStart(ProcessJobEvent $event)
    {
        $queue = $event->getQueue();
        $job   = $event->getJob();

        $this->logger->info(sprintf(
            $this->tmpl['job'],
            $queue->getName(),
            'START',
            $this->formatJob($job),
            ''
        ));

        if ($job instanceOf LoggerAwareInterface) {
            $job->setLogger($this->logger);
        }
    }

    public function logJobEnd(ProcessJobEvent $event)
    {
        $result  = $event->getResult();
        $job     = $event->getJob();
        $queue   = $event->getQueue()->getName();

        switch ($result) {
            default:

                $this->logger->info(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'SUCCESS',
                    $this->formatJob($job)
                ));
                break;

            case ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE:
                $reason = $job->getMetadata('log.reason');
                $this->logger->warn(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'RECOVERABLE',
                    $this->formatJob($job),
                    ": $reason"
                ));

                break;

            case ProcessJobEvent::JOB_STATUS_FAILURE:
                $reason = $job->getMetadata('log.reason');
                $this->logger->err(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'FAILURE',
                    $this->formatJob($job),
                    ": $reason"
                ));

                break;
        }
    }

    private function formatJob($job)
    {
        return get_class($job) . ' [ ' . $job->getId() . ' ] ';
    }

}
