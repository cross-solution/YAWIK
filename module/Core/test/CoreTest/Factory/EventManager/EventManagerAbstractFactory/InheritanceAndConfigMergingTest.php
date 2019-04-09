<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\EventManager\EventManagerAbstractFactory;

use PHPUnit\Framework\TestCase;

use Core\Factory\EventManager\EventManagerAbstractFactory;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Factory\EventManager\EventManagerAbstractFactory
 *
 * @covers \Core\Factory\EventManager\EventManagerAbstractFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.EventManager
 * @group Core.Factory.EventManager.EventManagerAbstractFactory
 */
class InheritanceAndConfigMergingTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var EventManagerAbstractFactory
     */
    protected $target = [
        '\Core\Factory\EventManager\EventManagerAbstractFactory',
        '@testCanCreateServiceWithName' => ['mock' => ['canCreate' => 1]],
    ];

    protected $inheritance = [ '\Zend\ServiceManager\Factory\AbstractFactoryInterface' ];

    public function testDeterminesIfItCanCreateAnEventManagerByName()
    {
        $services = new ServiceManager();
        $this->assertTrue(
            $this->target->canCreate(
                 $services,
                 'Any.string/Value/Events'
             ),
            'Checking correct name failed.'
        );
        $this->assertFalse(
            $this->target->canCreate(
                 $services,
                 'Any.string.not.ending/in/Events.but has it in the middle!'
             ),
            'Checking invalid name failed.'
        );
    }

    public function provideConfigMergingTestData()
    {
        $config1 = [];

        $config2 = [
            'event_manager' => [ 'Test2/Events' => [
                'service' => 'TestService',
                'configure' => false,
                'identifiers' => [ 'TestEvents', 'AnotherId' ],
                'event' => 'SomeEventClass',
                'listeners' => [ 'listener' => 'event' ]
            ]]
        ];
        return [
            [ $config1, 'Test1/Events' ],
            [ $config2, 'Test2/Events' ],
        ];
    }

    /**
     * @dataProvider provideConfigMergingTestData
     *
     * @param array $config
     * @param string $reqName
     */
    public function testMergesDefaultConfigWithProvidedConfig($config, $reqName)
    {
        $defaults = [
            'service' => 'EventManager',
            'configure' => true,
            'identifiers' => [ $reqName ],
            'event' => '\Zend\EventManager\Event',
            'listeners' => [],
        ];

        /* @var EventManagerAbstractFactory|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->getMockBuilder('\Core\Factory\EventManager\EventManagerAbstractFactory')
                       ->setMethods([ 'createEventManager', 'attachListeners' ])
                       ->getMock();

        $services = new ServiceManager();
        $services->setService('Config', $config);

        if (isset($config['event_manager'][$reqName])) {
            $expected = array_replace_recursive($defaults, $config['event_manager'][$reqName]);
        } else {
            $expected = $defaults;
        }

        $target->expects($this->once())->method('createEventManager')->with($services, $expected);

        $target($services, $reqName);
    }

    public function testCanCreateServiceWithName()
    {
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceLocatorInterface')->getMockForAbstractClass();

        $this->target->canCreateServiceWithName($services, 'irrelevant', 'irrelevant');
    }
}
