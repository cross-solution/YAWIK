<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Exception;

/**
 * Base JobException class.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class AbstractJobException extends \RuntimeException implements JobExceptionInterface
{
    /**
     * Options for the queue.
     *
     * @var array
     */
    protected $options = [];

    /**
     * AbstractJobException constructor.
     *
     * @param string|null $message
     * @param array       $options Options for the queue
     */
    public function __construct($message = null, array $options = [])
    {
        parent::__construct($message);

        $this->setOptions($options);
    }

    public function getOptions() : array
    {
        return array_merge(
            [
                'message' => $this->getMessage(),
                'trace'   => $this->getTraceAsString()
            ],
            $this->options
        );

    }

    public function setOptions(array $options) : void
    {
        $this->options = $options;
    }


}
