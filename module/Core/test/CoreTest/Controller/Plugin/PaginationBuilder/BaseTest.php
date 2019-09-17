<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller\Plugin\PaginationBuilder;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Controller\Plugin\PaginationBuilder
 *
 * @covers \Core\Controller\Plugin\PaginationBuilder
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 * @group Core.Controller.Plugin
 * @group Core.Controller.Plugin.PaginationBuilder
 */
class BaseTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var \Core\Controller\Plugin\PaginationBuilder
     */
    protected $target = [
        'class' => '\Core\Controller\Plugin\PaginationBuilder',
        '@testInvokationCallsGetResult' => [
            'mock' => ['getResult' => ['count' => 2, 'return' => '__self__']],
        ],
        '@testInvokationSetsUpStack' => [
            'mock' => [
                'params' => 1,
                'paginator',
                'form',
            ],
        ],
    ];

    protected $inheritance = [ '\Zend\Mvc\Controller\Plugin\AbstractPlugin' ];

    public function testInvokationWithoutArgumentsReturnsSelf()
    {
        $this->assertSame($this->target, $this->target->__invoke());
    }

    public function testInvokationWithBooleanTrueResetsStack()
    {
        $this->target->__invoke(['test' => 'Dummydata'], false);
        $this->assertSame($this->target, $this->target->__invoke(true), 'resetting stack does not return self!');
        $this->assertAttributeEmpty('stack', $this->target);
    }

    public function invalidArgumentProvider()
    {
        return [
            'string' => ['string is invalid'],
            'bool' => [false],
            'int' => [1234],
            'float' => [12.34],
        ];
    }

    /**
     * @dataProvider invalidArgumentProvider
     *
     * @param mixed $argument
     */
    public function testInvokationWithInvalidArgumentThrowsException($argument)
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Expected argument to be of type array');

        $this->target->__invoke($argument);
    }

    public function testInvokationCallsGetResult()
    {
        $this->assertSame($this->target, $this->target->__invoke(['Test' => 'Dummy data']));
        $this->target->__invoke(['Test2'], true);
    }

    public function argumentsStackProvider()
    {
        return [
            [ 'paginator', ['paginator'], ['as' => 'paginator', 'paginator', []] ],
            [ 'paginator', ['name', 'alias'], ['as' => 'alias', 'name', []] ],
            [ 'paginator', ['name', ['param' => 'value'], 'alias'], ['as' => 'alias', 'name', ['param' => 'value']] ],
            [ 'paginator', ['name', []], ['as' => 'paginator', 'name', []]],
            [ 'form', ['formName'], ['as' => 'searchform', 'formName', null]],
            [ 'form', ['formName', ['testOpt' => 'testVal']], ['as' => 'searchform', 'formName', ['testOpt' => 'testVal']]],
            [ 'form', ['formName', null, 'alias'], ['as' => 'alias', 'formName', null]],
            [ 'form', ['formName', 'alias'], ['as' => 'alias', 'formName', null]],
            [ 'params', ['namespace'], ['namespace', ['page' => 1]]],
            [ 'params', ['namespace', ['param' => 'value']], ['namespace', ['param' => 'value']]],
        ];
    }

    /**
     * @dataProvider argumentsStackProvider
     *
     * @param $method
     * @param $args
     * @param $expect
     */
    public function testSetPluginArgumentsStack($method, $args, $expect)
    {
        $this->assertSame($this->target, call_user_func_array([$this->target, $method], $args), 'Fluent interface broken!');
        $this->assertAttributeEquals([$method => $expect], 'stack', $this->target);
    }

    /**
     *
     */
    public function testInvokationSetsUpStack()
    {
        $this->target->expects($this->exactly(2))->method('paginator')
            ->withConsecutive(
                [ 'Name' ],
                [ 'Name', 'Alias']
            )->willReturn($this->returnSelf());

        $this->target->expects($this->exactly(2))->method('form')
            ->withConsecutive(
                [ 'Name' ],
                [ 'Name', 'Alias']
            )->willReturn($this->returnSelf());

        $stack = [
            'params' => ['Namespace'],
            'paginator' => ['Name'],
            'form' => ['Name'],
            'something' => ['ShouldNotBeConsidered'],
        ];

        $this->target->__invoke($stack, false);

        $stack = [
            'paginator' => ['as' => 'Alias', 'Name'],
            'form' => ['as' => 'Alias', 'Name'],
        ];

        $this->target->__invoke($stack, false);
    }
}
