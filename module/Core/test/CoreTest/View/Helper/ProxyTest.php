<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\View\Helper;

use Core\View\Helper\Proxy;
use Core\View\Helper\Proxy\NoopHelper;
use Core\View\Helper\Proxy\NoopIterator;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\ConsoleRenderer;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Renderer\RendererInterface;

/**
 * Tests for \Core\View\Helper\Proxy
 * 
 * @covers \Core\View\Helper\Proxy
 * @covers \Core\View\Helper\Proxy\NoopHelper
 * @covers \Core\View\Helper\Proxy\NoopIterator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 */
class ProxyTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|Proxy
     */
    private $target = [
        Proxy::class,
        '@testInvokationInvokesInvokableHelpers' => '#mockPlugin',
        '@testInvokationReturnsNonInvokablePlugin' => '#mockPlugin',
        '@testInvokationReturnsExpectedValues' => '#mockPlugin',
        '@testExistsProxiesToPlugin' => ['mock' => ['plugin' => ['with' => ['helper', true]]]],
        '#mockPlugin' => [
            'mock' => ['plugin'],
        ],
    ];

    private $inheritance = [ AbstractHelper::class ];

    /**
     *
     *
     * @var \CoreTestUtils\Mock\ServiceManager\PluginManagerMock
     */
    private $helperManager;

    private function injectHelperManager($services = [])
    {
        $manager = $this->createPluginManagerMock($services);

        $renderer = $this->getMockBuilder(PhpRenderer::class)->disableOriginalConstructor()
            ->setMethods(['getHelperPluginManager'])->getMock();

        $renderer->expects($this->any())->method('getHelperPluginManager')->will($this->returnValue($manager));

        $this->target->setView($renderer);
        $this->helperManager = $manager;
    }

    public function testInvokationReturnsProxyHelperWhenNoArguments()
    {
        $this->assertSame($this->target, $this->target->__invoke());
    }

    public function testInvokationInvokesInvokableHelpers()
    {
        $invokableHelper = new PtInvokableHelperDummy();
        $this->target->expects($this->exactly(2))->method('plugin')->with('invokableHelper')->willReturn($invokableHelper);

        $result = $this->target->__invoke('invokableHelper');

        $this->assertEquals('returnFromInvoke', $result);
        $this->assertTrue($invokableHelper->isInvoked);

        $invokableHelper->reset();
        $this->target->__invoke('invokableHelper', ['arg1', 'arg2']);

        $this->assertEquals(['arg1', 'arg2'], $invokableHelper->args);
    }

    public function testInvokationReturnsNonInvokablePlugin()
    {
        $helper = new PtHelperDummy();

        $this->target->expects($this->once())->method('plugin')->willReturn($helper);

        $result = $this->target->__invoke('helper');

        $this->assertSame($helper, $result);
    }

    public function testInvokationReturnsExpectedValues()
    {
        $this->target->expects($this->any())->method('plugin')->willReturn(false);

        $result = $this->target->__invoke('helper', ['arg1']);

        $this->assertInstanceOf(NoopHelper::class, $result);

        $result = $this->target->__invoke('helper', Proxy::EXPECT_ARRAY);

        $this->assertEquals([], $result);

        $result = $this->target->__invoke('helper', Proxy::EXPECT_ITARATOR);

        $this->assertInstanceOf(NoopIterator::class, $result);

        $result = $this->target->__invoke('helper', 'fancyoutput');

        $this->assertEquals('fancyoutput', $result);
    }

    public function testPluginReturnsFalseIfRendererDoesNotHaveHelperManager()
    {
        $renderer = new ConsoleRenderer();
        $this->target->setView($renderer);

        $this->assertFalse($this->target->plugin('helper'));
    }

    public function testPluginReturnsFalseIfPluginDoesNotExist()
    {
        $this->injectHelperManager();

        $this->assertFalse($this->target->plugin('helper'));
    }

    public function testPluginReturnsBool()
    {
        $this->injectHelperManager();

        $this->assertFalse($this->target->plugin('helper', true));

        $this->helperManager->setService('helper', new PtHelperDummy());

        $this->assertTrue($this->target->plugin('helper', true));
    }

    public function testPluginFetchesHelperFromManager()
    {
        $helper = new PtHelperDummy();
        $this->injectHelperManager(['helper' => $helper]);

        $this->assertSame($helper, $this->target->plugin('helper'));
    }

    public function testPluginPassesOptionsToHelperManager()
    {
        $helper = new PtHelperDummy();
        $options = ['option' => 'value'];
        $this->injectHelperManager(['helper' => $helper]);
        $this->helperManager->setExpectedCallCount('get', 'helper', $options, 1);

        $this->target->plugin('helper', $options);
    }

    public function testExistsProxiesToPlugin()
    {
        $this->target->exists('helper');
    }

    public function testNoopHelpers()
    {
        $helper = new NoopHelper();
        $this->assertNull($helper->__call('anyMethod', []));
        $this->assertEmpty($helper->__toString());
        $this->assertNull($helper->__get('anyProperty'));
        $this->assertFalse($helper->__isset('anyProperty'));
        $iterator = new NoopIterator();
        $this->assertInstanceOf('\IteratorAggregate', $iterator);
        $this->assertInstanceOf('\ArrayIterator', $iterator->getIterator());
        $this->assertEquals(0, $iterator->getIterator()->count());
    }

}

class PtHelperDummy
{

}

class PtInvokableHelperDummy
{
    public $isInvoked = false;
    public $args;

    public function __invoke() {
        $this->isInvoked = true;
        $this->args = func_get_args();

        return 'returnFromInvoke';
    }

    public function reset()
    {
        $this->isInvoked = false;
        $this->args = null;
    }
}