<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener;

use Core\Listener\ModuleVersionAdminWidgetProvider;
use Core\Listener\ModuleVersionAdminWidgetProviderFactory;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Core\Listener\ModuleVersionAdminWidgetProviderFactory
 * 
 * @covers \Core\Listener\ModuleVersionAdminWidgetProviderFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class ModuleVersionAdminWidgetProviderFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var string|ModuleVersionAdminWidgetProviderFactory
     */
    private $target = ModuleVersionAdminWidgetProviderFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreatesListener()
    {
        $moduleManagerMock = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $container = $this->getServiceManagerMock(['ModuleManager' => ['service' => $moduleManagerMock, 'count_get' => 1]]);

        $listener  = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(ModuleVersionAdminWidgetProvider::class, $listener);
    }
}
