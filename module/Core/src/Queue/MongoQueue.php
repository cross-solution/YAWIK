<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\AbstractQueue;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class MongoQueue extends AbstractQueue
{

    const STATUS_PENDING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FAILED  = 3;

    const DEFAULT_PRIORITY = 1024;

    /**
     * Used to synchronize time calculations
     *
     * @var \DateTime
     */
    private $now;

    /**
     * @var \MongoDB\Collection
     */
    private $mongoCollection;

    /**
     * Constructor
     *
     * @param Connection       $connection
     * @param string           $tableName
     * @param string           $name
     * @param JobPluginManager $jobPluginManager
     */
    public function __construct(
        \MongoDB\Collection $collection,
        $name,
        JobPluginManager $jobPluginManager
    ) {
        $this->mongoCollection = $collection;

        parent::__construct($name, $jobPluginManager);
    }


    /**
     * Valid options are:
     *      - priority: the lower the priority is, the sooner the job get popped from the queue (default to 1024)
     *
     * {@inheritDoc}
     *
     * Note : see DoctrineQueue::parseOptionsToDateTime for schedule and delay options
     */
    public function push(JobInterface $job, array $options = [])
    {
        $envelope = $this->createEnvelope($job, $options);

        $result = $this->mongoCollection->insertOne($envelope);

        $job->setId((string) $result->getInsertedId());
    }

    private function createEnvelope(JobInterface $job, array $options = [])
    {
        $scheduled = $this->parseOptionsToDateTime($options);
        $tried     = isset($options['tried']) ? (int) $options['tried'] : null;
        $message   = isset($options['message']) ? $options['message'] : null;
        $trace     = isset($options['trace']) ? $options['trace'] : null;

        $envelope = [
            'queue'     => $this->getName(),
            'status'    => self::STATUS_PENDING,
            'tried'     => $tried,
            'message'   => $message,
            'trace'     => $trace,
            'created'   => $this->dateTimeToUTCDateTime($this->now),
            'data'      => $this->serializeJob($job),
            'scheduled' => $this->dateTimeToUTCDateTime($scheduled),
            'priority'  => isset($options['priority']) ? $options['priority'] : self::DEFAULT_PRIORITY,
        ];

        return $envelope;
    }

    public function retry(JobInterface $job, array $options = [])
    {
        $tried = $job->getMetadata('mongoqueue.tries', 0) + 1;
        $job->setMetaData('mongoqueue.tries', $tried);

        $options['tried'] = $tried;
        $envelope = $this->createEnvelope($job, $options);
        unset($envelope['created']);

        $this->mongoCollection->findOneAndUpdate(
            [
                '_id' => new \MongoDB\BSON\ObjectID($job->getId())
            ],
            [
                '$set' => $envelope
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function pop(array $options = [])
    {
        $time      = microtime(true);
        $micro     = sprintf("%06d", ($time - floor($time)) * 1000000);
        $this->now = new \DateTime(
            date('Y-m-d H:i:s.' . $micro, $time),
            new \DateTimeZone(date_default_timezone_get())
        );
        $now = $this->dateTimeToUTCDateTime($this->now);

        $envelope = $this->mongoCollection->findOneAndUpdate(
            [
                'queue' => $this->getName(),
                'status' => self::STATUS_PENDING,
                'scheduled' => ['$lte' => $now],
            ],
            [
                '$set' => [
                    'status' => self::STATUS_RUNNING,
                    'executed' => $now,
                ],
            ],
            [
                'sort' => ['priority' => 1, 'scheduled' => 1],
                'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
            ]
        );

        if (!$envelope) {
            return null;
        }

        return $this->unserializeJob($envelope['data'], ['__id__' => $envelope['_id']]);
    }

    public function listing(array $options = [])
    {
        $filter = [ 'queue' => $this->getName() ];
        if (isset($options['status'])) {
            $filter['status'] = $options['status'];
        }

        $opt = [ 'sort' => [ 'priority' => 1, 'scheduled' => 1] ];
        if (isset($options['limit'])) {
            $opt['limit'] = $options['limit'];
        }

        $cursor = $this->mongoCollection->find($filter, $opt);
        $jobs   = $cursor->toArray();

        foreach ($jobs as &$envelope) {
            $envelope['job'] = $this->unserializeJob($envelope['data'], ['__id__' => $envelope['_id']]);
        }

        return $jobs;

    }

    /**
     * {@inheritDoc}
     *
     * Note: When $deletedLifetime === 0 the job will be deleted immediately
     */
    public function delete(JobInterface $job, array $options = [])
    {
        $result = $this->mongoCollection->deleteOne(['_id' => $job->getId()]);

        return (bool) $result->getDeletedCount();
    }

    /**
     * {@inheritDoc}
     *
     * Note: When $buriedLifetime === 0 the job will be deleted immediately
     */
    public function fail(JobInterface $job, array $options = [])
    {
        $envelope = $this->createEnvelope($job, $options);
        unset($envelope['created']);
        unset($envelope['scheduled']);
        $envelope['status'] = self::STATUS_FAILED;

        $this->mongoCollection->findOneAndUpdate(
            [
                '_id' => new \MongoDB\BSON\ObjectId($job->getId())
            ],
            [
                '$set' => $envelope
            ]
        );
    }

    /**
     * Parses options to a datetime object
     *
     * valid options keys:
     *
     * scheduled: the time when the job will be scheduled to run next
     * - numeric string or integer - interpreted as a timestamp
     * - string parserable by the DateTime object
     * - DateTime instance
     * delay: the delay before a job become available to be popped (defaults to 0 - no delay -)
     * - numeric string or integer - interpreted as seconds
     * - string parserable (ISO 8601 duration) by DateTimeInterval::__construct
     * - string parserable (relative parts) by DateTimeInterval::createFromDateString
     * - DateTimeInterval instance
     *
     * @see http://en.wikipedia.org/wiki/Iso8601#Durations
     * @see http://www.php.net/manual/en/datetime.formats.relative.php
     *
     * @param $options array
     * @return \DateTime
     */
    protected function parseOptionsToDateTime($options)
    {
        $time      = microtime(true);
        $micro     = sprintf("%06d", ($time - floor($time)) * 1000000);
        $this->now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time), new \DateTimeZone(date_default_timezone_get()));
        $scheduled = clone ($this->now);

        if (isset($options['scheduled'])) {
            switch (true) {
                case is_numeric($options['scheduled']):
                    $scheduled = new \DateTime(
                        sprintf("@%d", (int) $options['scheduled']),
                        new \DateTimeZone(date_default_timezone_get())
                    );
                    break;
                case is_string($options['scheduled']):
                    $scheduled = new \DateTime($options['scheduled'], new \DateTimeZone(date_default_timezone_get()));
                    break;
                case $options['scheduled'] instanceof \DateTime:
                    $scheduled = $options['scheduled'];
                    break;
            }
        }

        if (isset($options['delay'])) {
            switch (true) {
                case is_numeric($options['delay']):
                    $delay = new \DateInterval(sprintf("PT%dS", abs((int) $options['delay'])));
                    $delay->invert = ($options['delay'] < 0) ? 1 : 0;
                    break;
                case is_string($options['delay']):
                    try {
                        // first try ISO 8601 duration specification
                        $delay = new \DateInterval($options['delay']);
                    } catch (\Exception $e) {
                        // then try normal date parser
                        $delay = \DateInterval::createFromDateString($options['delay']);
                    }
                    break;
                case $options['delay'] instanceof \DateInterval:
                    $delay = $options['delay'];
                    break;
                default:
                    $delay = null;
            }

            if ($delay instanceof \DateInterval) {
                $scheduled->add($delay);
            }
        }

        return $scheduled;
    }

    protected function dateTimeToUTCDateTime(\DateTime $date)
    {
        return new \MongoDB\BSON\UTCDateTime($date->getTimestamp() * 1000);
    }

}
