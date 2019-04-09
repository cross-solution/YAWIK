<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\EventManager\EventManager;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\EventManager\EventManager
 *
 * @covers \Core\EventManager\EventManager
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.EventManager
 */
class BaseTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var \Core\EventManager\EventManager
     */
    protected $target = '\Core\EventManager\EventManager';

    protected $inheritance = [ '\Zend\EventManager\EventManager', '\Core\EventManager\EventProviderInterface' ];

    public function testSetEventPrototype()
    {
        $event = new MockEvent();
        $this->target->setEventPrototype($event);
        $this->assertAttributeSame($event, 'eventPrototype', $this->target, 'Setting event prototype object failed.');
    }

    public function provideGetEventTestData()
    {
        $testTarget = new \stdClass();
        $testTarget2 = new \stdClass();
        $testParams1 = [ 'params1' => 'value1' ];
        $testParams2 = [ 'name' => 'test6', 'param2' => 'value2' ];
        $testParams3 = [ 'name' => 'test8', 'target' => $testTarget, 'param3' => 'value3' ];
        $expect2     = [ 'param2' => 'value2' ];
        $expect3     = [ 'param3' => 'value3' ];

        return [
            [ 'test1', null, [], [ 'name' => 'test1', 'target' => null, 'params' => []]],
            [ 'test2', $testTarget, [], [ 'name' => 'test2', 'target' => $testTarget, 'params' => []]],
            [ 'test3', null, $testParams1, [ 'name' => 'test3', 'target' => null, 'params' => $testParams1]],
            'setParamsAsFirstArg' => [ $testParams1, null, ['ignoreme' => 'yes' ], [ 'name' => null, 'target' => null, 'params' => $testParams1 ]],
            'setParamsAsSecondArg' => [ 'test5', $testParams1, ['ignoreme' => 'yes' ], ['name' => 'test5', 'target' => null, 'params' => $testParams1 ]],
            'setNameInParamsAsFirstArg' => [ $testParams2, null, [], [ 'name' => 'test6', 'target' => null, 'params' => $expect2]],
            'setNameInParamsAsFirstArgAndTarget' => [ $testParams2, $testTarget, ['ignoreme' => 'yes'], [ 'name' => 'test6', 'target' => $testTarget, 'params' => $expect2]],
            'setNameAndTargetInParams' => [ $testParams3, null, [], [ 'name' => 'test8', 'target' => $testTarget, 'params' => $expect3]],
            [ 'test9', $testTarget2, $testParams3, ['name' => 'test9', 'target' => $testTarget2, 'params' => $testParams3 ]],
            [ null, $testParams2, [], ['name' => 'test6', 'target' => null, 'params' => $expect2 ]],

        ];
    }

    /**
     * @dataProvider provideGetEventTestData
     *
     * @param string $name
     * @param object $target
     * @param array $params
     * @param array $expects
     */
    public function testEventIsClonedAndPopulatedWithProvidedArguments($name, $target, $params, $expects)
    {
        $event = $this->target->getEvent($name, $target, $params);

        $this->assertEquals($expects['name'], $event->getName(), 'Name is not set correct.');
        $this->assertSame($expects['target'], $event->getTarget(), 'Target is not set correct.');
        $this->assertEquals($expects['params'], $event->getParams(), 'Params are not set correct.');
    }
}

class MockEvent extends \Zend\EventManager\Event
{
}
