<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Constraint to assert the existence and default values of attributes.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 * @since 0.29 Allow passing in a \ReflectionClass instance.
 */
class DefaultAttributesValues extends Constraint
{
    /**
     * The default attributes.
     *
     * Must be an array with propertyName => value pairs.
     *
     * @var array
     */
    private $defaultAttributes = [];

    /**
     * Stores the result of each test for internal use.
     *
     * @var array
     */
    private $result = [];

    /**
     * Creates a new instance.
     *
     * @param array $defaultAttributes
     */
    public function __construct($defaultAttributes = [])
    {
        $this->defaultAttributes = $defaultAttributes;
        parent::__construct();
    }

    public function count(): int
    {
        return count($this->defaultAttributes);
    }

    /**
     * Tests if an object has the required attributes and they have the correct value.
     *
     * Returns true, if and only if the object defines ALL attributes and they have the expected value
     *
     * @param object $other
     * @return bool
     * @throws \ReflectionException
     * @since 0,29 Allow passing in a Reflection class instance.
     */
    protected function matches($other): bool
    {
        $this->result = [];
        $success      = true;

        $reflection = $other instanceof \ReflectionClass ? $other : new \ReflectionClass($other);
        $properties = $reflection->getDefaultProperties();

        foreach ($this->defaultAttributes as $prop => $value) {
            if (is_int($prop)) {
                $prop = $value;
                $value = null;
            }

            if (array_key_exists($prop, $properties)) {
                try {
                    Assert::assertSame($value, $properties[$prop]);
                    $this->result[$prop] = true;
                } catch (ExpectationFailedException $e) {
                    $message = $e->toString();

                    if ($comparisonFailure = $e->getComparisonFailure()) {
                        $message .= sprintf(
                            "\n%30sExpected: %s\n%30sActual  : %s\n",
                            '',
                            $comparisonFailure->getExpectedAsString(),
                            '',
                            $comparisonFailure->getActualAsString()
                        );
                    }

                    $this->result[$prop] = $message;
                    $success = false;
                }
            } else {
                $this->result[$prop] = 'Attribute is not defined.';
                $success = false;
            }
        }

        return $success;
    }

    protected function failureDescription($other): string
    {
        return ($other instanceof \ReflectionClass ? $other->getName() : get_class($other)) . ' ' . $this->toString();
    }

    protected function additionalFailureDescription($other): string
    {
        $info = '';

        foreach ($this->result as $prop => $msg) {
            if (true === $msg) {
                $info .= "\n + $prop";
            } else {
                $info .= sprintf("\n - %-25s: %s", $prop, $msg);
            }
        }

        return $info;
    }

    public function toString(): string
    {
        return 'has expected default attributes and its values.';
    }
}
