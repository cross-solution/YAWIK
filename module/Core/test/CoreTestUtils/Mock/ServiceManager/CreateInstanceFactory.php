<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\Mock\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory that creates an instance from a class name and passing constructor arguments along.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class CreateInstanceFactory implements FactoryInterface
{
    /**
     * Class name
     *
     * @var string
     */
    protected $class;

    /**
     * Constructor arguments.
     *
     * Strings starting with '@' are considered to be service names.
     *
     * @var array
     */
    protected $args = [];

    /**
     * Creates an instance.
     *
     * @param string $class
     * @param array  $args
     */
    public function __construct($class, array $args = [])
    {
        $this->class = $class;
        $this->args  = $args;
    }

    /**
     * Creates a service instance.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (empty($this->args)) {
            return new $this->class;
        }

        $reflection = new \ReflectionClass($this->class);
        $args       = array_map(
            function ($arg) use ($serviceLocator) {
                return is_string($arg) && 0 === strpos($arg, '@') ? $serviceLocator->get(substr($arg, 1)) : $arg;
            },
            $this->args
        );

        $instance = $reflection->newInstanceArgs($args);

        return $instance;
    }
}