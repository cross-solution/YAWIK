<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener\Events;

use PHPUnit\Framework\TestCase;

use Core\Listener\Events\AjaxEvent;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\EventManager\Event;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

/**
 * Tests for \Core\Listener\Events\AjaxEvent
 *
 * @covers \Core\Listener\Events\AjaxEvent
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class AjaxEventTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = AjaxEvent::class;

    private $inheritance = [ Event::class ];

    public function propertiesProvider()
    {
        $request = new Request();
        $response = new Response();
        return [
            ['contentType', [
                'value' => 'test/type',
                'default' => AjaxEvent::TYPE_JSON,
                'post' => function () {
                    $this->assertEquals('test/type', $this->target->getParam('contentType'));
                },
            ]],
            ['contentType', [
                'pre' => function () {
                    $this->target->setParam('contentType', 'type/test');
                },
                'value' => 'type/test',
                'ignore_setter' => true,
            ]],

            ['request', [
                'value' => $request,
                'default' => null,
                'post' => function () use ($request) {
                    $this->assertSame($request, $this->target->getParam('request'));
                },
            ]],
            ['request', [
                'pre' => function () use ($request) {
                    $this->target->setParam('request', $request);
                },
                'value' => $request,
                'ignore_setter' => true,
            ]],

            ['response', [
                'value' => $response,
                'default' => null,
                'post' => function () use ($response) {
                    $this->assertSame($response, $this->target->getParam('response'));
                },
            ]],
            ['response', [
                'pre' => function () use ($response) {
                    $this->target->setParam('response', $response);
                },
                'value' => $response,
                'ignore_setter' => true,
            ]],


            ['result', [
                'value' => 'testResult',
                'default' => null,
                'post' => function () {
                    $this->assertEquals('testResult', $this->target->getParam('result'));
                },
            ]],
            ['result', [
                'pre' => function () {
                    $this->target->setParam('result', 'testResult');
                },
                'value' => 'testResult',
                'ignore_setter' => true,
            ]],

        ];
    }
}
