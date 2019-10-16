<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Decorator;

use PHPUnit\Framework\TestCase;

use Core\Decorator\ProxyDecorator;

/**
 * Tests for \Core\Decorator\ProxyDecorator
 *
 * @covers \Core\Decorator\ProxyDecorator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Decorator
 */
class ProxyDecoratorTest extends TestCase
{
    protected function setUp(): void
    {
        $this->object = new ObjectMock();
        $this->target = new ProxyDecoratorMock($this->object);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot proxy "unknownMethod" to "CoreTest\Decorator\ObjectMock": Unknown method.
     */
    public function testProxyToUnknownMethodThrowsException()
    {
        $this->target->testProxy('unknownMethod');
    }

    public function testProxyToMethod()
    {
        $this->target->testProxy('objectMethod', 'arg1', 'arg2', 'arg3');

        $this->assertTrue($this->object->areAllArgumentsCorrect, 'Argument values does not match!');
        $this->assertTrue($this->object->wasCalledWithCorrectArgumentCount, 'Argument count was incorrect');
    }

    public function testProxyToMethodReturnsExpectedResults()
    {
        $returnSelf = $this->target->testProxy('returnSelf');
        $returnValue = $this->target->testProxy('returnValue');

        $this->assertSame($this->target, $returnSelf);
        $this->assertEquals('someValue', $returnValue);
    }
}

class ProxyDecoratorMock extends ProxyDecorator
{
    protected $objectType = '\CoreTest\Decorator\ObjectMock';

    public function testProxy()
    {
        return call_user_func_array(array($this, 'proxy'), func_get_args());
    }
}

class ObjectMock
{
    public $areAllArgumentsCorrect = false;
    public $wasCalledWithCorrectArgumentCount = false;

    public function objectMethod($arg1, $arg2, $arg3)
    {
        $this->areAllArgumentsCorrect = 'arg1' == $arg1 && 'arg2' == $arg2 && 'arg3' == $arg3;
        $this->wasCalledWithCorrectArgumentCount = 3 == func_num_args();
    }

    public function returnSelf()
    {
        return $this;
    }

    public function returnValue()
    {
        return 'someValue';
    }
}
