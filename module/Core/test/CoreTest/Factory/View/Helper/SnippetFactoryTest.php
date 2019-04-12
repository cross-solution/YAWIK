<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\EventManager\EventManager;
use Core\Factory\View\Helper\SnippetFactory;
use Core\View\Helper\Snippet;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Helper\Partial;

/**
 * Tests for \Core\Factory\View\Helper\SnippetFactory
 *
 * @covers \Core\Factory\View\Helper\SnippetFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.View
 * @group Core.Factory.View.Helper
 */
class SnippetFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|SnippetFactory
     */
    private $target = [
        SnippetFactory::class,
        '@testCreateService' => ['mock' => ['__invoke']],
    ];

    private $inheritance = [ FactoryInterface::class ];


    public function testCreateService()
    {
        $container = $this->createServiceManagerMock();
        $plugins = $this->createPluginManagerMock($container, 1);

        $this->target
            ->expects($this->once())
            ->method('__invoke')
            ->with($container, Snippet::class)
        ;

        $this->target->createService($container);
    }

    public function testInvokationCreatesService()
    {
        $events = new EventManager();
        $partialHelper = new Partial();
        $config = ['test' => 'works'];
        
        $container = $this->createServiceManagerMock([
            'Config' => ['service' => ['view_helper_config' => ['snippets' => $config]], 'count_get' => 1, 'direct' => true],
            'Core/ViewSnippets/Events' => ['service' => $events, 'count_get' => 1],
            //'ViewHelperManager' => ['service' => $helpers, 'count_get' => 1],
        ]);
        $helpers = $this->createPluginManagerMock([
                'partial' => ['service' => $partialHelper, 'count_get' => 1],
            ], $container);
        $container->setService('ViewHelperManager', $helpers);

        $instance = $this->target->__invoke($container, Snippet::class);

        $this->assertInstanceOf(Snippet::class, $instance);
        $this->assertAttributeSame($partialHelper, 'partials', $instance);
        $this->assertAttributeSame($events, 'events', $instance);
        $this->assertAttributeSame($config, 'config', $instance);
    }
}
