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

use Jobs\Factory\JobEventManagerFactory;
use Zend\EventManager\EventManager;

/**
 * Tests for JobEventManagerFactory
 *
 * @covers \Jobs\Factory\JobEventManagerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Jobs
 * @group Jobs.Factory
 */
class JobEventManagerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new JobEventManagerFactory());
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
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('EventManager')->willReturn($eventManager);

        $events = $target->__invoke($services,'irrelevant');

        $this->assertSame($eventManager, $events);
        $this->assertAttributeInstanceOf($expectedEventClass, 'eventPrototype', $events);
        $this->assertEquals($expectedIdentifiers, $events->getIdentifiers());
    }
}
