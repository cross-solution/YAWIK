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

/**
 * Constraint to assert the existence and default values of attributes.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 */
class DefaultAttributesValues extends \PHPUnit_Framework_Constraint
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

    public function count()
    {
        return count($this->defaultAttributes);
    }

    /**
     * Tests if an object has the required attributes and they have the correct value.
     *
     * Returns true, if and only if the object defines ALL attributes and they have the expected value
     *
     * @param object $other
     *
     * @return bool
     */
    protected function matches($other)
    {
        $this->result = [];
        $success      = true;

        $reflection = new \ReflectionClass($other);
        $properties = $reflection->getDefaultProperties();

        foreach ($this->defaultAttributes as $prop => $value) {
            if (array_key_exists($prop, $properties)) {
                try {
                    \PHPUnit_Framework_Assert::assertSame($value, $properties[$prop]);
                    $this->result[$prop] = true;
                } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
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

    protected function failureDescription($other)
    {
        return get_class($other) . ' ' . $this->toString();
    }

    protected function additionalFailureDescription($other)
    {
        $info = '';

        foreach ($this->result as $prop => $msg) {
            if (true === $msg) {
                $info .= "\n + $prop";
            } else {
                $info .= sprintf("\n - %-25s: %s",  $prop, $msg);
            }
        }

        return $info;
    }

    public function toString()
    {
        return 'has expected default attributes and its values.';
    }


}