<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Worker;

use Core\Queue\Exception\FatalJobException;
use Core\Queue\Exception\RecoverableJobException;
use Core\Queue\MongoQueue;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class MongoWorker extends AbstractWorker
{
    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        if (!$queue instanceof MongoQueue) {
            return;
        }

        try {
            $job->execute($queue);
            $queue->delete($job);

            return ProcessJobEvent::JOB_STATUS_SUCCESS;

        } catch (RecoverableJobException $exception) {
            $queue->retry($job, $exception->getOptions());

            return ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE;

        } catch (FatalJobException $exception) {
            $queue->fail($job, $exception->getOptions());

            return ProcessJobEvent::JOB_STATUS_FAILURE;
        }
    }
}
