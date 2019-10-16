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

use Core\Controller\AdminControllerEvent;
use Core\Listener\ModuleVersionAdminWidgetProvider;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Zend\ModuleManager\ModuleManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Core\Listener\ModuleVersionAdminWidgetProvider
 * 
 * @covers \Core\Listener\ModuleVersionAdminWidgetProvider
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class ModuleVersionAdminWidgetProviderTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var array|ModuleVersionAdminWidgetProvider
     */
    private $target = [
        ModuleVersionAdminWidgetProvider::class,
        'setupModuleManagerMock',
    ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ModuleManager
     */
    private $moduleManagerMock;

    private function setupModuleManagerMock()
    {
        $this->moduleManagerMock = $this->getMockBuilder(ModuleManager::class)
                                        ->disableOriginalConstructor()
                                        ->setMethods(['getLoadedModules'])
                                        ->getMock();

        return [ $this->moduleManagerMock ];
    }

    public function testConstructorInjectsDependencies()
    {
        $this->assertAttributeSame($this->moduleManagerMock, 'moduleManager', $this->target);
    }

    private function getModuleMock($name)
    {
        $module = new \stdClass();
        $module->name = $name;

        return $module;
    }

    private function getModulesArrays()
    {
        $coreModule = $this->getModuleMock('Core');
        $jobsModule = $this->getModuleMock('Jobs');
        $aModule    = $this->getModuleMock('A');
        $zendModule = $this->getModuleMock('Zend\Something');
        $doctModule = $this->getModuleMock('DoctrineAnything');

        return [
            [
                'Core' => $coreModule,
                'Jobs' => $jobsModule,
                'A'    => $aModule,
                'Zend\Something' => $zendModule,
                'DoctrineAnything' => $doctModule,
            ],
            [
                'A' => $aModule,
                'Core' => $coreModule,
                'Jobs' => $jobsModule,

            ]
        ];
    }

    public function testInvokation()
    {
        list($actualModules, $expectedModules) = $this->getModulesArrays();

        $this->moduleManagerMock->expects($this->once())->method('getLoadedModules')->willReturn($actualModules);

        /* @var \PHPUnit_Framework_MockObject_MockObject|AdminControllerEvent $event */
        $event = $this->getMockBuilder(AdminControllerEvent::class)->disableOriginalConstructor()
            ->setMethods(['addViewTemplate'])->getMock();

        $event->expects($this->once())->method('addViewTemplate')
            ->with(
                'modules', 'core/admin/module-version-widget.phtml',
                $this->callback(function($arg) use ($expectedModules) {
                    /* Test for correct ordering by compare the arrays of the keys (which are numerical)
                     * test for correct content with the === operator. */
                    return array_keys($arg['modules']) === array_keys($expectedModules) && $arg['modules'] === $expectedModules;
                }),
                100
            );

        $this->target->__invoke($event);
    }
}
