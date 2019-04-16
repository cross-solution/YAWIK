<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTestUtils\TestCase;

use PHPUnit\Framework\TestCase;

/**
 * Class InitValueTrait
 *
 * @package CoreTestUtils\TestCase
 */
trait InitValueTrait
{
    /**
     * @param   mixed $object
     * @param   string $propName
     * @param   mixed $expectedValue
     * @dataProvider getTestInitValue
     */
    public function testInitValue($object, $propName, $expectedValue)
    {
        /* @var $this \PHPUnit\Framework\TestCase */

        $getter = 'get' . $propName;
        if (is_object($expectedValue)) {
            $this->assertInstanceOf(
                get_class($expectedValue),
                $object->$getter(),
                '::' . $getter . '() init value should return a type of ' . get_class($expectedValue)
            );
        } elseif (is_array($expectedValue)) {
            $this->assertSame(
                $expectedValue,
                $object->$getter(),
                '::' . $getter . '() init value should return an empty array'
            );
        } else {
            $this->assertEquals(
                $expectedValue,
                $object->$getter(),
                '::' . $getter . '() init value should return ' . $expectedValue
            );
        }
    }

    abstract public function getTestInitValue();
}
