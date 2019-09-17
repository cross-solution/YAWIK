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
 * SlmQueue implementation for a queue backed by MongoDB
 *
 * Heavily inspired by https://github.com/juriansluiman/SlmQueueDoctrine
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MongoQueue extends AbstractQueue
{

    /**#@+
     * Job status
     * @var int
     */
    const STATUS_PENDING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FAILED  = 3;
    /**#@-*/

    /**
     * Default priority
     * @var int
     */
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
     * @param \MongoDB\Collection $collection
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
     * Push a job to the queue.
     *
     * Valid options are:
     *      - priority: the lower the priority is, the sooner the job get popped from the queue (default to 1024)
     *
     * Note : see {@link parseOptionsToDateTime()} for schedule and delay options
     *
     * @param JobInterface $job
     * @param array $options
     */
    public function push(JobInterface $job, array $options = [])
    {
        $envelope = $this->createEnvelope($job, $options);

        $result = $this->mongoCollection->insertOne($envelope);

        $job->setId((string) $result->getInsertedId());
    }

    /**
     * Push a lazy loading job in the queue.
     *
     * Lazy job allows to load the actual job only when executed.
     *
     * You can specify the job to load in two ways:
     * - as string:
     *      The actual job will be pulled from the job manager or instantiated from a valid class name.
     *
     * - as array:
     *      If you need to pass options to the job, you can specify the
     *      job as an array:
     *      [ string:name, array:options ]
     *      The actual job will be pulled from the job manager using the 'build' command,
     *      passing the options along OR if no service for the job is defined, but a valid
     *      class name is given, this class will be instantiated and the options are passed
     *      as constructor arguments,
     *
     * @see push()
     * @see LazyJob
     *
     * @param string|array $service
     * @param mixed|null  $payload
     * @param array $options
     */
    public function pushLazy($service, $payload = null, array $options = [])
    {
        $manager = $this->getJobPluginManager();
        $serviceOptions = [];

        if (is_array($service)) {
            $serviceOptions = $service['options'] ?? $service[1] ?? [];
            $service = $service['name'] ?? $service[0] ?? null;
        }

        if (!$manager->has($service) && !class_exists($service)) {
            throw new \UnexpectedValueException(sprintf(
                'Service name "%s" is not a known job service or existent class',
                $service
            ));
        }

        $lazyOptions = [
            'name' => $service,
            'options' => $serviceOptions,
            'content' => $payload,
        ];

        $job = $this->getJobPluginManager()->build('lazy', $lazyOptions);

        $this->push($job, $options);
    }

    /**
     * Create a mongo document.
     *
     * @param JobInterface $job
     * @param array        $options
     *
     * @return array
     */
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

    /**
     * Reinsert the job in the queue.
     *
     * @see push()
     * @param JobInterface $job
     * @param array        $options
     */
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
     * Pop a job from the queue.
     *
     * The status will be set to self::STATUS_RUNNING.
     *
     * @param array $options unused
     * @return null|JobInterface
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

    /**
     * Fetch a list of jobs
     *
     * @param array $options
     *
     * @return array
     */
    public function listing(array $options = [])
    {
        $filter = [ 'queue' => $this->getName() ];
        if (isset($options['status'])) {
            $filter['status'] = $options['status'];
        }

        $opt = [ 'sort' => [ 'scheduled' => 1, 'priority' => 1] ];
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
     * Delete a job from the queue
     *
     * @param JobInterface $job
     * @param array        $options unused
     *
     * @return bool
     */
    public function delete(JobInterface $job, array $options = [])
    {
        $result = $this->mongoCollection->deleteOne(['_id' => $job->getId()]);

        return (bool) $result->getDeletedCount();
    }

    /**
     * Mark a job as permanent failed.
     *
     * The status will be set to self::STATUS_FAILED
     * @param JobInterface $job
     * @param array        $options unused
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
     * @codeCoverageIgnore
     * @param $options array
     * @return \DateTime
     */
    protected function parseOptionsToDateTime($options)
    {
        $time      = microtime(true);
        $micro     = sprintf("%06d", ($time - floor($time)) * 1000000);
        $this->now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time), new \DateTimeZone(date_default_timezone_get()));
        $scheduled = isset($options['scheduled']) ? Utils::createDateTime($options['scheduled']) : clone ($this->now);

        if (isset($options['delay'])) {
            $delay = Utils::createDateInterval($options['delay']);
            $scheduled->add($delay);
        }

        return $scheduled;
    }

    /**
     * Converst a \DateTime object to its UTCDateTime representation.
     *
     * @param \DateTime $date
     *
     * @return \MongoDB\BSON\UTCDateTime
     */
    protected function dateTimeToUTCDateTime(\DateTime $date)
    {
        return new \MongoDB\BSON\UTCDateTime($date->getTimestamp() * 1000);
    }

}
