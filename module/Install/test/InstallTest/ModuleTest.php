<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest;

use PHPUnit\Framework\TestCase;

use Install\Module;
use Core\Service\Tracy;

/**
 * Tests for \Install\Module
 *
 * @covers \Install\Module
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 */
class ModuleTest extends TestCase
{

    /**
     * Path as seen from target,
     *
     * @var string
     */
    protected $moduleDir;

    /**
     * Module name space
     *
     * @var string
     */
    protected $moduleNamespace = 'Install';

    /**
     * Class under test.
     *
     * @var Module
     */
    protected $target;

    protected function setUp(): void
    {
        $this->moduleDir = realpath(__DIR__ . '/../../');
        $this->target    = new Module();
    }
    /**
     * @testdox Implements required Feature Interfaces.
     */
    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf('\Zend\ModuleManager\Feature\ConfigProviderInterface', $this->target, 'Module class does not implement ConfigProviderInterface');
        $this->assertInstanceOf('\Zend\ModuleManager\Feature\BootstrapListenerInterface', $this->target, 'Module class does not implement BootstrapListenerInterface');
    }

    public function testProvidesCorrectConfigArray()
    {
        $config = include $this->moduleDir . '/config/module.config.php';

        $this->assertEquals($config, $this->target->getConfig());
    }

    /**
     * @testdox Attachs language setter listener on bootstrap event
     */
    public function testOnBootstrapListener()
    {
        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')->disableOriginalConstructor()->getMock();

        $listener = $this->getMockBuilder('\Install\Listener\LanguageSetter')->disableOriginalConstructor()->getMock();
        $listener->expects($this->once())->method('attach')->with($events);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        // expects Tracy::startDebug to be called
        $tracy = $this->getMockBuilder(Tracy::class)->disableOriginalConstructor()->getMock();
        $tracy->expects($this->once())->method('startDebug');

        //$services->expects($this->once())->method('get')->with('Install/Listener/LanguageSetter')->willReturn($listener);
        $services->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['Install/Listener/LanguageSetter'],
                ['Tracy']
            )
            ->will($this->onConsecutiveCalls(
                $listener,
                $tracy
            ))
        ;

        $application = $this->getMockBuilder('\Zend\Mvc\Application')->disableOriginalConstructor()->getMock();
        $application->expects($this->once())->method('getEventManager')->willReturn($events);
        $application->expects($this->once())->method('getServiceManager')->willReturn($services);

        $event = $this->getMockBuilder('\Zend\Mvc\MvcEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $event->expects($this->once())->method('getApplication')->willReturn($application);

        $this->assertNull($this->target->onBootstrap($event));
    }
}
