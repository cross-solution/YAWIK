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

use Core\Queue\Job\JobResult;
use Core\Queue\Job\ResultProviderInterface;
use Core\Queue\MongoQueue;
use Core\Queue\Utils;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JobResultStrategy extends AbstractStrategy
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'handleJobResult'], -999);
    }

    public function handleJobResult(ProcessJobEvent $event)
    {
        $result = $event->getResult();
        $queue = $event->getQueue();
        $job   = $event->getJob();
        $logger = $event->getParam('logger');

        if (!$queue instanceOf MongoQueue) {
            return;
        }

        $result = $job instanceOf ResultProviderInterface ? $job->getResult() : new JobResult($result);

        if ($result->isSuccess()) {
            $queue->delete($job);
            if ($reason = $result->getReason()) {
                $logger && $logger->info($reason, $result->getExtra() ?? []);
            }

            return;
        }

        if ($result->isFailure()) {
            $reason = $result->getReason();
            $extra  = $result->getExtra();

            $queue->fail($job, ['message' => $reason, 'trace' => $extra]);

            $logger && $logger->err($reason, $extra ?? []);

            return;
        }

        if ($result->isRecoverable()) {
            $reason = $result->getReason();
            $extra  = $result->getExtra();
            $delay  = $result->getDelay();
            $date   = $result->getDate();

            $options = ['message' => $reason, 'trace' => $extra];

            $logger && $logger->warn($reason, $extra ?? []);

            if ($delay) {
                $logger && $logger->notice('Will retry in ' . $this->formatDelay($delay));
                $options['delay'] = $delay;

            } elseif ($date) {
                $logger && $logger->notice('Will retry on ' . $this->formatScheduled($date));
                $options['scheduled'] = $date;
            }

            $queue->retry($job, $options);

            return;
        }

        $logger && $logger->warn('Unsupported job result: ' . $result->getResult() . '; Job will be deleted.');
        $queue->delete($job);

    }

    private function formatDelay($delay)
    {
        $delay = Utils::createDateInterval($delay);
        $parts = [];
        if ($delay->y) {
            $parts[] = $delay->y . ' years';
        }
        if ($delay->m) {
            $parts[] = $delay->m . ' months';
        }
        if ($delay->d) {
            $parts[] = $delay->d . ' days';
        }
        if ($delay->h) {
            $parts[] = $delay->h . ' hours';
        }
        if ($delay->i) {
            $parts[] = $delay->i . ' minutes';
        }
        if ($delay->s) {
            $parts[] = $delay->s . ' seconds';
        }

        return join(', ', $parts);
    }

    private function formatScheduled($scheduled)
    {
        $date = Utils::createDateTime($scheduled);
        return $date->format('d.m.Y H:i:s');
    }

}
