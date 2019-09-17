<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Queue;

use Core\Queue\Exception\FatalJobException;
use Core\Queue\Job\MongoJob;
use Core\Queue\LoggerAwareJobTrait;
use Jobs\Repository\Job;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueAwareTrait;
use SlmQueue\Queue\QueueInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FindJobsWithExternalImageJob extends MongoJob implements QueueAwareInterface, LoggerAwareInterface
{
    use QueueAwareTrait, LoggerAwareJobTrait;

    /**
     *
     *
     * @var Job
     */
    private $repository;

    public function __construct(Job $repository = null)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        if (!$this->repository) {
            return $this->failure('Cannot execute without repository.');
        }

        $logger = $this->getLogger();
        $qb = $this->repository->createQueryBuilder();
        $qb->field('logoRef')->equals(new \MongoDB\BSON\Regex('^https?:\/\/', 'i'));
        $qb->limit(10);
        $query = $qb->getQuery();
        $cursor = $query->execute();

        $queue = $this->getQueue();

        if (!$cursor->count()) {
            $logger->info('No jobs with external images found. Reinsert with delay 2h.');
            $queue->push(self::create(), ['delay' => '+2 hours']);

            return $this->success();
        }

        $invalidJobs = 0;
        foreach ($cursor->toArray() as $job) {
            if (0 === strpos($job->getLogoRef(), 'http')) {
                $queue->push(FetchExternalImageJob::create($job));
                $logger->debug('Found external image uri: ' . $job->getLogoRef());
                $logger->info('Pushed fetch image job for Job: '  . $job->getId());
            } else {
                $invalidJobs += 1;
            }
        }

        $delay = 0 >= ($cursor->count() - $invalidJobs) ? '+2 hours' : '+5 minutes';
        $queue->push(self::create(), ['delay' => $delay]);
        $logger->info('Reinserted to fetch more jobs with delay: ' . $delay);

        return $this->success();
    }

}
