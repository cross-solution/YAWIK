<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\View\Helper\Proxy;

use PHPUnit\Framework\TestCase;

use Core\View\Helper\Proxy\HelperProxy;
use CoreTestUtils\TestCase\SetupTargetTrait;

/**
 * Tests for \Core\View\Helper\Proxy\HelperProxy
 *
 * @covers \Core\View\Helper\Proxy\HelperProxy
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 * @group Core.View.Helper.Proxy
 */
class HelperProxyTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var array|HelperProxy|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        'class' => HelperProxy::class,
        'args'  => [false],
        '@testInvokation' => [
            'args' => false,
            'mock' => ['call'],
        ],
        '@testMagicCalls' => '@testInvokation',
        '@testMagicCallsWithExpectValue' => '@testInvokation',
        '@testCallWithHelper' => false,
        '@testChain' => false,
        '@testConsecutive' => false,
    ];

    public function testConstruction()
    {
        $this->assertFalse($this->target->helper());
    }

    public function testInvokation()
    {
        $this->target->expects($this->once())->method('call')->with('__invoke', ['arg']);

        $this->target->__invoke('arg');
    }

    public function testMagicCalls()
    {
        $this->target->expects($this->once())->method('call')->with('method', ['arg'], null);

        $this->target->__call('method', ['arg']);
    }

    public function testMagicCallsWithExpectValue()
    {
        $this->target->expects($this->once())->method('call')->with('Method', ['arg1', 'arg2'], 'expectedValue');

        $this->target->callMethod('arg1', 'arg2', 'expectedValue');
    }

    public function provideCallTestData()
    {
        return [
            [['method', ['arg1']], '__self__'],
            [['method'], '__self__'],
            [['method', 'expect'], 'expect'],
            [['method', HelperProxy::EXPECT_ARRAY], []],
            [['method', HelperProxy::EXPECT_ITERATOR], '__iterator__'],
            [['method', false], false],
        ];
    }

    /**
     * @dataProvider provideCallTestData
     *
     * @param $args
     * @param $expect
     */
    public function testCallWithNoHelper($args, $expect)
    {
        $actual = call_user_func_array([$this->target, 'call'], $args);

        if ('__self__' == $expect) {
            $this->assertSame($actual, $this->target);
        } elseif ('__iterator__' == $expect) {
            $this->assertInstanceOf(\ArrayIterator::class, $actual);
        } else {
            $this->assertEquals($actual, $expect);
        }
    }

    public function testCallWithHelper()
    {
        $helper = new Hp_DummyHelper();
        $target = new HelperProxy($helper);

        $target->call('method', ['args']);

        $this->assertEquals(['method' => ['args']], $helper->stack);
    }


    public function testChain()
    {
        $helper = new HpChainableHelper();
        $target = new HelperProxy($helper);

        $chain = [
            ['invokeArg', 'anotherInvokeArg'],
            'method1' => ['arg1', 'arg2'],
            'method2',
        ];

        $target->chain($chain);


        $this->assertEquals(['__invoke' => $chain[0]], $helper->stack);
        $helper = $helper->helper;
        $this->assertEquals(['method1' => $chain['method1']], $helper->stack, 'Second chained helper call failed.');
        $helper = $helper->helper;
        $this->assertEquals(['method2' => []], $helper->stack, 'Third chained helper call failed.');
    }

    public function testChainReturnsExpectedValueWhenHelperDoesNotExists()
    {
        $expect = 'expectedValue';
        $actual = $this->target->chain(['popel'], $expect);

        $this->assertEquals($expect, $actual);
    }

    public function testConsecutive()
    {
        $helper = new HpConsecutiveHelper();
        $target = new HelperProxy($helper);

        $chain = [
            ['invokeArg'],
            'method1',
            'method2' => ['arg2', 'arg1'],
            'method3' => 'lonelyArg',
        ];

        $expect = [
            '__invoke' => $chain[0],
            'method1' => [],
            'method2' => $chain['method2'],
            'method3' => [$chain['method3']],
        ];

        $this->assertSame($target, $target->consecutive($chain));
        $this->assertEquals($expect, $helper->stack);
    }
}

class Hp_DummyHelper
{
    public $stack = [];

    public function __call($method, $args)
    {
        $this->stack[$method] = $args;
    }
}

class HpConsecutiveHelper
{
    public $stack = [];

    public function __call($method, $args)
    {
        $this->stack[$method] = $args;
        return;
    }
}

class HpChainableHelper extends HpConsecutiveHelper
{
    public $helper;

    public function __call($method, $args)
    {
        parent::__call($method, $args);
        $this->helper = new self();
        return $this->helper;
    }
}
