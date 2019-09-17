<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */

declare(strict_types=1);

/** */
namespace Core\Queue\Strategy;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Job\JobInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\FinishEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * Queue Worker Strategy to log events using an instance of {@link LoggerInterface}
 *
 * If the processed job implements LoggerAwareInterface, the Logger gets injected
 * into the job - unless {@link injectLogger} is false.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class LogStrategy extends AbstractStrategy
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Templates for log messages
     *
     * @var array
     */
    private $tmpl = [
        'queue' => '%s queue: %s',
        'job'   => '{ %s } [ %s ] %s',
    ];

    /**
     * Inject the logger in the processed job?
     *
     * @var bool
     */
    private $injectLogger = true;

    /**
     * LogStrategy constructor.
     *
     * @param array|LoggerInterface|null $options
     */
    public function __construct($options = null)
    {
        if (null === $options) { return; }

        if ($options instanceOf LoggerInterface) {
            $options = ['logger' => $options];
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('Options must be of type array or ' . LoggerInterface::class);
        }

        parent::__construct($options);
    }

    /**
     * Set the logger instance
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(Loggerinterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger instance
     *
     * If no instance is set yet, it will return an instance of
     * an anonymous class implemeting LoggerInterface which does nothing.
     *
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        if (!$this->logger) {
            $logger = new class implements LoggerInterface
            {
                public function emerg($message, $extra = []) : void {}
                public function alert($message, $extra = []) : void {}
                public function crit($message, $extra = []) : void {}
                public function err($message, $extra = []) : void {}
                public function warn($message, $extra = []) : void {}
                public function notice($message, $extra = []) : void {}
                public function info($message, $extra = []) : void {}
                public function debug($message, $extra = []) : void {}
            };
            $this->setLogger($logger);
        }

        return $this->logger;
    }

    /**
     * Set the template for queue events log messages
     *
     * @param string $template
     */
    public function setLogQueueEventsTemplate(string $template) : void
    {
        $this->tmpl['queue'] = $template;
    }

    /**
     * Set the template for job events log messages.
     *
     * @param string $template
     */
    public function setLogJobEventsTemplate(string $template) : void
    {
        $this->tmpl['job'] = $template;
    }

    /**
     * Set or get the injectLogger flag
     *
     * If called with an argument, sets the flag accordingly and returns
     * it.
     *
     * @param bool|null $flag
     * @return bool
     */
    public function injectLogger(bool $flag = null) : bool
    {
        if (null === $flag) { return $this->injectLogger; }

        $this->injectLogger = $flag;
        return $flag;
    }

    /**
     * Registers itself with an EventManager
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1) : void
    {
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_BOOTSTRAP, [$this, 'logBootstrap'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_FINISH, [$this, 'logFinish'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobStart'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobEnd'], -1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_IDLE, [$this, 'injectLoggerInEvent'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_STATE, [$this, 'injectLoggerInEvent'], 1000);
    }

    /**
     * Listener method for queue bootstrap event
     *
     * @param BootstrapEvent $event
     */
    public function logBootstrap(BootstrapEvent $event) : void
    {
        $this->getLogger()->info(sprintf(
            $this->tmpl['queue'],
            'Start',
            $event->getQueue()->getName()
        ));

        $this->injectLoggerInObject($event->getWorker());
        $this->injectLoggerInEvent($event);
    }

    /**
     * Listener method for queue finish event
     *
     * @param FinishEvent $event
     */
    public function logFinish(FinishEvent $event) : void
    {
        $this->getLogger()->info(sprintf(
            $this->tmpl['queue'],
            'Stop',
            $event->getQueue()->getName()
        ));

        $this->injectLoggerInEvent($event);
    }

    /**
     * listener method for process job event.
     *
     * Called early due to high priority.
     *
     * @param ProcessJobEvent $event
     */
    public function logJobStart(ProcessJobEvent $event) : void
    {
        $queue  = $event->getQueue();
        $job    = $event->getJob();
        $logger = $this->getLogger();

        $logger->info(sprintf(
            $this->tmpl['job'],
            $queue->getName(),
            'START',
            $this->formatJob($job),
            ''
        ));

        $this->injectLoggerInObject($job);
        $this->injectLoggerInEvent($event);
    }

    /**
     * listener method for process job event
     *
     * Called late due to low priority
     *
     * @param ProcessJobEvent $event
     */
    public function logJobEnd(ProcessJobEvent $event) : void
    {
        $result  = $event->getResult();
        $job     = $event->getJob();
        $queue   = $event->getQueue()->getName();
        $logger  = $this->getLogger();

        switch ($result) {
            default:

                $logger->info(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'SUCCESS',
                    $this->formatJob($job)
                ));
                break;

            case ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE:
                $logger->warn(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'RECOVERABLE',
                    $this->formatJob($job)
                ));

                break;

            case ProcessJobEvent::JOB_STATUS_FAILURE:
                $logger->err(sprintf(
                    $this->tmpl['job'],
                    $queue,
                    'FAILURE',
                    $this->formatJob($job)
                ));

                break;
        }
    }

    public function injectLoggerInEvent(AbstractWorkerEvent $event)
    {
        if ($this->injectLogger()) {
            $event->setParam('logger', $this->getLogger());
        }
    }

    private function injectLoggerInObject($object) : void
    {
        if ($this->injectLogger() && $object instanceOf LoggerAwareInterface) {
            $object->setLogger($this->getLogger());
        }
    }

    /**
     * Get a string representation of the processed job instance
     *
     * @param JobInterface $job
     *
     * @return string
     */
    private function formatJob(JobInterface $job) : string
    {
        return get_class($job) . ' [ ' . $job->getId() . ' ] ';
    }

}
