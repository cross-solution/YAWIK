<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\View\Helper\Alert as Helper;

class AlertTest extends TestCase
{

    /**
     * @var Helper
     */
    private $helper;

    protected function setUp(): void
    {
        $this->helper = new Helper();
    }

    public function testInvocation()
    {
        $helper = $this->helper;
        $this->assertInstanceOf(Helper::class, $helper());

        $type = Helper::TYPE_DANGER;
        $content = 'some content';
        $options = ['some options'];
        $return = 'return';
        /* @var Helper $helper */
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['render'])
            ->getMock();
        $helper->expects($this->once())
            ->method('render')
            ->with($this->equalTo($type), $this->equalTo($content), $this->equalTo($options))
            ->willReturn($return);
        $this->assertSame($return, $helper($type, $content, $options));
    }

    public function testRenderCallsStartMethod()
    {
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['start'])
            ->getMock();
        $helper->expects($this->once())
            ->method('start')
            ->with($this->equalTo(null), $this->equalTo([]))
            ->willReturnSelf();
        $this->assertSame($helper, $helper->render());

        $options = ['some options'];
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['start'])
            ->getMock();
        $helper->expects($this->once())
            ->method('start')
            ->with($this->equalTo(Helper::TYPE_INFO), $this->equalTo($options))
            ->willReturnSelf();
        $this->assertSame($helper, $helper->render($options));

        $type = Helper::TYPE_DANGER;
        $options = ['some options'];

        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['start'])
            ->getMock();
        $helper->expects($this->exactly(2))
            ->method('start')
            ->with($this->equalTo($type), $this->equalTo($options))
            ->willReturnSelf();
        $this->assertSame($helper, $helper->render($type, $options));
        $this->assertSame($helper, $helper->render($type, true, $options));
    }

    public function testRenderDoesNotCallStartMethod()
    {
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['start'])
            ->getMock();
        $helper->expects($this->never())
            ->method('start');

        $this->assertTrue(is_string($helper->render(Helper::TYPE_INFO, 'message')));
        $this->assertTrue(is_string($helper->render(Helper::TYPE_INFO, 'message', [])));
        $this->assertTrue(is_string($helper->render(Helper::TYPE_INFO, 'message', ['some options'])));
    }

    /**
     * @dataProvider typeContentData
     */
    public function testRenderWithTwoScalarParams($type, $content)
    {
        $markup = $this->helper->render($type, $content);

        $this->assertTrue(is_string($markup));
        $this->assertContains($type, $markup);
        $this->assertContains($content, $markup);
    }

    public function testClassOptionDoesNotOverrideClassByType()
    {
        $type = Helper::TYPE_SUCCESS;
        $classViaOptions = 'classPassedViaOptions';
        $content = 'message';
        $markup = $this->helper->render($type, $content, ['class' => $classViaOptions]);

        $this->assertTrue(is_string($markup));
        $this->assertContains($type, $markup);
        $this->assertContains($classViaOptions, $markup);
    }

    public function testIdOption()
    {
        $id = 'some id';
        $markup = $this->helper->render(Helper::TYPE_INFO, 'message', ['id' => $id]);

        $this->assertTrue(is_string($markup));
        $this->assertRegExp('/id=(["\'])'.$id.'\1/', $markup);
    }

    public function testDismissableOption()
    {
        $type = Helper::TYPE_SUCCESS;
        $content = 'message';

        $markup = $this->helper->render($type, $content);
        $this->assertTrue(is_string($markup));
        $this->assertContains('dismissable', $markup);

        $markup = $this->helper->render($type, $content, ['dismissable' => true]);
        $this->assertTrue(is_string($markup));
        $this->assertContains('dismissable', $markup);

        $markup = $this->helper->render($type, $content, ['dismissable' => false]);
        $this->assertTrue(is_string($markup));
        $this->assertNotContains('dismissable', $markup);
    }

    public function testStartReturnsSelf()
    {
        $this->assertSame($this->helper, $this->helper->start());
        ob_get_clean(); // silence PHPUnit "risky test" notice
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage already a capture running
     */
    public function testStartCalledTwiceConsecutivelyThrowsException()
    {
        $this->helper->start();
        ob_get_clean(); // silence PHPUnit "risky test" notice
        $this->helper->start();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage no capture running
     */
    public function testEndCalledWithoutCallingStartThrowsException()
    {
        $this->helper->end();
    }

    public function testEndOfCapturingCallsRenderMethodProperly()
    {
        $type = Helper::TYPE_WARNING;
        $content = 'content to capture';
        $options = ['some options'];
        $return = 'return';
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['render'])
            ->getMock();
        $helper->expects($this->once())
            ->method('render')
            ->with($this->equalTo($type), $this->equalTo($content), $this->equalTo($options))
            ->willReturn($return);

        $helper->start($type, $options);
        echo $content;
        $this->assertSame($return, $helper->end());
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Unknown method
     */
    public function testMethodOverloadingThrowsException()
    {
        $this->helper->invalidMethodCall();
    }

    /**
     * @dataProvider typeContentData
     */
    public function testMethodOverloadingCallsRenderMethodProperly($type, $content)
    {
        $options = ['some options'];
        $return = 'return';
        $helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['render'])
            ->getMock();
        $helper->expects($this->once())
            ->method('render')
            ->with($this->equalTo($type), $this->equalTo($content), $this->equalTo($options))
            ->willReturn($return);

        $this->assertSame($return, $helper->$type($content, $options));
    }

    public function typeContentData()
    {
        return [
            [Helper::TYPE_INFO, 'inf-msg'],
            [Helper::TYPE_SUCCESS, 'suc-msg'],
            [Helper::TYPE_WARNING, 'war-msg'],
            [Helper::TYPE_DANGER, 'dan-msg'],
        ];
    }
}
