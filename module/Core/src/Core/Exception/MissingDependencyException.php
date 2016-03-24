<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Exception;

/**
 * This exception is thrown when an object is missing a required dependency.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class MissingDependencyException extends \RuntimeException implements ExceptionInterface
{

    /**
     * FQCN of the missing dependent class
     *
     * @var string
     */
    protected $dependency;

    /**
     * FCQN or the concrete instance of the object missing the dependency
     *
     * @var string|object
     */
    protected $target;

    /**
     * Creates an instance.
     *
     * @param string     $dependency The FQCN of the missing dependency object.
     * @param string|object $object FQCN of or the object itself in which the dependency is missing.
     * @param \Exception $previous
     */
    public function __construct($dependency, $object, \Exception $previous = null)
    {
        $this->dependency = $dependency;
        $this->target     = $object;

        $message = sprintf(
            'Missing dependency "%s" in "%s"',
            $dependency,
            $this->getTargetFQCN()
        );

        parent::__construct($message, 0, $previous);
    }

    /**
     * Gets the FQCN of the dependency
     *
     * @return string
     */
    public function getDependency()
    {
        return $this->dependency;
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
