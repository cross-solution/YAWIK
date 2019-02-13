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
namespace Core\Queue\Job;

use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JobResult
{
    /**
     *
     *
     * @var int
     */
    protected $result;

    /**
     *
     *
     * @var string
     */
    protected $reason;

    /**
     *
     *
     * @var array
     */
    protected $extra;

    /**
     *
     *
     * @var string|int|\DateInterval
     */
    protected $delay;

    /**
     *
     *
     * @var string|int|\DateTime
     */
    protected $scheduled;

    public static function success(?string $reason = null, ?array $extra = null) : self
    {
        return (new static(ProcessJobEvent::JOB_STATUS_SUCCESS))
            ->withReason($reason)
            ->withExtra($extra)
        ;
    }

    public static function failure(string $reason, ?array $extra = null) : self
    {
        return (new static(ProcessJobEvent::JOB_STATUS_FAILURE))
            ->withReason($reason)
            ->withExtra($extra)
        ;
    }

    public static function recoverable(string $reason, array $options = []) : self
    {
        $result = (new static(ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE))
            ->withReason($reason);

        foreach ($options as $key => $value) {
            $callback = [$result, "with$key"];
            if (is_callable($callback)) {
                $callback($value);
            }
        }

        return $result;
    }

    public function __construct(int $result)
    {
        $this->result = $result;
    }

    public function getResult() : int
    {
        return $this->result;
    }

    public function isSuccess() : bool
    {
        return ProcessJobEvent::JOB_STATUS_SUCCESS == $this->result;
    }

    public function isFailure() : bool
    {
        return ProcessJobEvent::JOB_STATUS_FAILURE == $this->result;
    }

    public function isRecoverable() : bool
    {
        return ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE == $this->result;
    }

    /**
     * @return string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function withReason($reason) : self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     *
     * @return self
     */
    public function withExtra(array $extra) : self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return \DateInterval|int|string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param \DateInterval|int|string $delay
     *
     * @return self
     */
    public function withDelay($delay) : self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return \DateTime|int|string
     */
    public function getDate()
    {
        return $this->scheduled;
    }

    /**
     * @param \DateTime|int|string $scheduled
     *
     * @return self
     */
    public function withDate($scheduled) : self
    {
        $this->scheduled = $scheduled;
    }
}
