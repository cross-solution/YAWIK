<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Listener;

use Core\Factory\Listener\AjaxRouteListenerFactory;
use Core\Listener\AjaxRouteListener;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Core\EventManager\EventManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Factory\Listener\AjaxRouteListenerFactory
 * 
 * @covers \Core\Factory\Listener\AjaxRouteListenerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Listener
 */
class AjaxRouteListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|AjaxRouteListenerFactory
     */
    private $target = [
        AjaxRouteListenerFactory::class,
        '@testCreateService' => [
            'mock' => ['__invoke'],
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    /**
     * @testdox Method createService() proxies to __invoke()
     */
    public function testCreateService()
    {
        $services = $this->getServiceManagerMock();
        $this->target->expects($this->once())->method('__invoke')->with($services, AjaxRouteListener::class);

        $this->target->createService($services);
    }

    public function testInvokationCreatesService()
    {
        $events = new EventManager();
        $services = $this->getServiceManagerMock([
                'Core/Ajax/Events' => ['service' => $events, 'count_get' => 1],
        ]);

        $listener = $this->target->__invoke($services, 'irrelevant');

        $this->assertInstanceOf(AjaxRouteListener::class, $listener);
        $this->assertAttributeSame($events, 'ajaxEventManager', $listener);
    }


}