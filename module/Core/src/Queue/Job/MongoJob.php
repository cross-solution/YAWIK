<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Job;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class MongoJob extends AbstractJob implements ResultProviderInterface
{
    protected $result;

    public static function create($payload = null)
    {
        $class = new \ReflectionClass(static::class);
        $instance = $class->newInstanceWithoutConstructor();

        if ($payload) {
            $payload = static::filterPayload($payload);
            $instance->setContent($payload);
        }

        return $instance;
    }

    protected static function filterPayload($payload)
    {
        return $payload;
    }

    public function setResult(JobResult $result) : void
    {
        $this->result = $result;
    }

    public function getResult() : JobResult
    {
        if (!$this->result) {
            $this->setResult(new JobResult(ProcessJobEvent::JOB_STATUS_UNKNOWN));
        }

        return $this->result;
    }

    protected function failure(string $message, ?array $extra = null) : int
    {
        $this->setResult(JobResult::failure($message, $extra));

        return ProcessJobEvent::JOB_STATUS_FAILURE;
    }

    protected function recoverable(string $message, array $options = []) : int
    {
        $this->setResult(JobResult::recoverable($message, $options));

        return ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE;
    }

    protected function success(?string $message = null, ?array $extra = null) : int
    {
        $this->setResult(JobResult::success($message, $extra));

        return ProcessJobEvent::JOB_STATUS_SUCCESS;
    }
}
