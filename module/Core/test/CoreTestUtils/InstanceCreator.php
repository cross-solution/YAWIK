<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTestUtils;

use PHPUnit\Framework\TestCase;

/**
 * Utility class to instantiates objects.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
final class InstanceCreator
{
    /**
     * Flag to force instantiation.
     *
     * @var bool
     */
    const FORCE_INSTANTIATION = true;

    /**
     * Static class only.
     */
    private function __construct()
    {
    }

    /**
     * Loops over an array and instantiates objects where a "instance"-syntax is detected.
     *
     * @param array $arr
     *
     * @return array
     */
    public static function mapArray($arr)
    {
        return array_map([self::class, 'fromSpec'], $arr);
    }

    /**
     * Creates an object instance from a specification.
     *
     * This is either a string with a FQCN (prefixes with '@', if <b>$force</b> is not TRUE),
     * or an array with two elements, where the first is the FQCN (prefixed, unless $force == true) and
     * the second is an array of constructor arguments (which itself can contain instance-specs)
     *
     * @param string|array $spec
     * @param bool $force
     *
     * @return object
     */
    public static function fromSpec($spec, $force = false)
    {
        if (is_string($spec) && ($force || 0 === strpos($spec, '@'))) {
            return self::newClass($spec);
        }

        if (is_array($spec) && isset($spec[0]) && ($force || 0 === strpos($spec[0], '@'))) {
            $args = isset($spec[1]) ? $spec[1] : null;

            return self::newClass($spec[0], $args);
        }

        return $spec;
    }

    /**
     * Creates an instance
     *
     * @param string $class
     * @param array|null $args
     *
     * @return object
     */
    public static function newClass($class, $args = null)
    {
        $class = ltrim($class, '@');

        if (null === $args) {
            return new $class;
        }

        $args = self::mapArray($args);

        $reflection = new \ReflectionClass($class);
        $instance   = $reflection->newInstanceArgs($args);

        return $instance;
    }
}
