<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTestUtils\TestCase;

use PHPUnit\Framework\TestCase;

trait SimpleSetterAndGetterTrait
{
    /**
     * @param $propertyName
     * @param $propertyValue
     * @dataProvider getSetterAndGetterDataProvider
     */
    final public function testSetterAndGetter($object, $propertyName, $propertyValue)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Data Provider first argument should be an object');
        }
        $setter = 'set' . $propertyName;
        $getter = 'get' . $propertyName;

        call_user_func(array($object, $setter), $propertyValue);
        $this->assertSame(
            $propertyValue,
            call_user_func(array($object, $getter)),
            '::' . $setter . '() and ::' . $getter . '() should executed properly'
        );
    }

    abstract public function getSetterAndGetterDataProvider();
}
