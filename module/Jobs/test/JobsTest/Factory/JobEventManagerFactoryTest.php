<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory;

use PHPUnit\Framework\TestCase;

use Jobs\Factory\JobEventManagerFactory;
use Laminas\EventManager\EventManager;

/**
 * Tests for JobEventManagerFactory
 *
 * @covers \Jobs\Factory\JobEventManagerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Jobs
 * @group Jobs.Factory
 */
class JobEventManagerFactoryTest extends TestCase
{

    /**
     * @testdox Implements \Laminas\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Laminas\ServiceManager\Factory\FactoryInterface', new JobEventManagerFactory());
    }

    public function testProvidesDefaultIdentifiers()
    {
        $expected = array('Jobs', 'Jobs/Events');

        $this->assertAttributeEquals($expected, 'identifiers', new JobEventManagerFactory());
    }

    public function testCreatesAnEventManagerWithEventClassAndIdentifiersSet()
    {
        $target = new JobEventManagerFactory();
        $expectedEventClass = '\Jobs\Listener\Events\JobEvent';
        $expectedIdentifiers = array('Jobs', 'Jobs/Events');
        $eventManager = new EventManager();
        $services = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('EventManager')->willReturn($eventManager);

        $events = $target->__invoke($services, 'irrelevant');

        $this->assertSame($eventManager, $events);
        $this->assertAttributeInstanceOf($expectedEventClass, 'eventPrototype', $events);
        $this->assertEquals($expectedIdentifiers, $events->getIdentifiers());
    }
}
