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
use Core\Queue\Job\ExceptionJobResult;
use Core\Queue\Job\JobResult;
use Core\Queue\Job\ResultProviderInterface;
use Core\Queue\LoggerAwareJobTrait;
use Core\Queue\MongoQueue;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\Log\LoggerAwareInterface;

/**
 * Queue worker for the mongo queue.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MongoWorker extends AbstractWorker implements LoggerAwareInterface
{
    use LoggerAwareJobTrait;

    /**
     * Process job handler.
     *
     * @param JobInterface   $job
     * @param QueueInterface $queue
     *
     * @return int|void
     */
    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        if (!$queue instanceof MongoQueue) {
            return;
        }

        if ($job instanceOf QueueAwareInterface) {
            $job->setQueue($queue);
        }

        try {
            return $job->execute();
        } catch (\Exception $exception) {
            $this->getLogger()->err('Job execution thrown exception: ' . get_class($exception));

            if ($job instanceOf ResultProviderInterface) {
                $job->setResult(JobResult::failure($exception->getMessage(), [$exception->getTraceAsString()]));
            } else {
                $this->getLogger()->err($exception->getMessage(), [$exception->getTraceAsString()]);
            }

            return ProcessJobEvent::JOB_STATUS_FAILURE;
        }
    }
}
