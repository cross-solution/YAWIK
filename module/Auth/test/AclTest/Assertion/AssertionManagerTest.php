<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AclTest\Assertion;

use Acl\Assertion\AssertionManager;
use MyProject\Proxies\__CG__\stdClass;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\Exception;

/**
 * Tests the AssertionManager
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AssertionManagerTest extends \PHPUnit_Framework_TestCase
{


    public function testExtendsCorrectParent()
    {
        $target = new AssertionManager();

        $this->assertInstanceOf('\Zend\ServiceManager\AbstractPluginManager', $target);
    }

    public function testConstructorAddsInitializer()
    {
        $target = new AssertionManagerMock();

        $this->assertTrue($target->wasAddInitializerCalledCorrectly());
    }

    public function testInjectEventManagerInitializerCallbackDoesNothingIfAssertionNotEventManagerAware()
    {
        $target = new AssertionManager();
        $assertion = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()
                         ->getMock();
        $services->expects($this->never())->method('getServiceLocator');
        $services->expects($this->never())->method('get');

        $this->assertNull($target->injectEventManager($assertion, $services));
    }

    public function testInjectEventManagerInitializerCallbackGetsEventManagerFromServicesIfNotSetInAssertion()
    {
        $target = new AssertionManager();
        $assertion = $this->getMockForAbstractClass('\AclTest\Assertion\EventManagerAwareAssertionMock');
        $services = $this->getMockForAbstractClass('\Zend\ServiceManager\AbstractPluginManager');
        $parentServices = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $events = new EventManager();

        $assertion->expects($this->once())->method('getEventManager')->willReturn(null);
        $assertion->expects($this->once())->method('setEventManager')->with($events);

        $parentServices->expects($this->once())->method('get')->with('EventManager')->willReturn($events);
        /*
         * Wanted to use:
         * //$services->expects($this->once())->method('getServiceLocator')->willReturn($parentServices);
         * but PHPUnit does not allow a concrete method in an abstract class to be mocked. :(
         *
         * So I have to do it this way:
         */
        $services->setServiceLocator($parentServices);

        $this->assertNull($target->injectEventManager($assertion, $services));
    }

    public function testInjectEventManagerInitializerCallbackSetsSharedEventManagerInEventsIfSetInAssertion()
    {
        $target = new AssertionManager();
        $assertion = $this->getMockForAbstractClass('\AclTest\Assertion\EventManagerAwareAssertionMock');
        $services = $this->getMockForAbstractClass('\Zend\ServiceManager\AbstractPluginManager');
        $parentServices = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $events = new EventManager();
        $sharedEvents = new SharedEventManager();

        $services->setServiceLocator($parentServices);

        $parentServices->expects($this->once())->method('get')->with('SharedEventManager')->willReturn($sharedEvents);
        $assertion->expects($this->once())->method('getEventManager')->willReturn($events);

        $this->assertNull($target->injectEventManager($assertion, $services));
        $this->assertSame($sharedEvents, $events->getSharedManager());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected plugin to be of type Assertion.
     */
    public function testValidatePluginThrowsExceptionIfPluginIsInvalid()
    {
        $target = new AssertionManager();
        $assertion = $this->getMockForAbstractClass('\Zend\Permissions\Acl\Assertion\AssertionInterface');

        $this->assertNull($target->validatePlugin($assertion));
        $target->validatePlugin(new \stdClass());
    }
}

class AssertionManagerMock extends AssertionManager
{
    private $addInitializerCalledCorrect = false;

    public function addInitializer($initializer, $topOfStack = true)
    {
        $this->addInitializerCalledCorrect = array($this, 'injectEventManager') === $initializer && false === $topOfStack;
        return parent::addInitializer($initializer, $topOfStack);
    }

    public function wasAddInitializerCalledCorrectly()
    {
        return $this->addInitializerCalledCorrect;
    }
}

abstract class EventManagerAwareAssertionMock implements AssertionInterface, EventManagerAwareInterface
{
}
