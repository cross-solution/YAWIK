<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Options\Exception;

/**
 * Exception is thrown, if a required option value is missing.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class MissingOptionException extends \RuntimeException implements ExceptionInterface
{
    /**
     * Target option class or FQCN.
     *
     * @var string|object
     */
    protected $target;

    /**
     * The option key.
     *
     * @var string
     */
    protected $optionKey;

    /**
     * Creates an instance.
     *
     * @param string     $optionKey
     * @param int        $target
     * @param \Exception $previous
     */
    public function __construct($optionKey, $target, \Exception $previous = null)
    {
        $this->optionKey = $optionKey;
        $this->target    = $target;

        $message = sprintf(
            'Missing value for option "%s" in "%s"',
            $optionKey,
            $this->getTargetFQCN()
        );

        parent::__construct($message, 0, $previous);
    }

    /**
     * Gets the option key.
     *
     * @return string
     */
    public function getOptionKey()
    {
        return $this->optionKey;
    }

    /**
     * Gets the target.
     *
     * @return object|string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Gets the target FQCN
     *
     * This will return the FQCN even if the target was provided as an concrete instance.
     *
     * @return string
     */
    public function getTargetFQCN()
    {
        return is_object($this->target) ? get_class($this->target) : (string) $this->target;
    }
}
