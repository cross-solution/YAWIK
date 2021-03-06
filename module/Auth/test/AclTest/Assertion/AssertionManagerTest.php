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

use PHPUnit\Framework\TestCase;

use Acl\Assertion\AssertionManager;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\SharedEventManager;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Tests the AssertionManager
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AssertionManagerTest extends TestCase
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->serviceManager = new ServiceManager();
    }

    public function testExtendsCorrectParent()
    {
        $target = new AssertionManager($this->serviceManager);

        $this->assertInstanceOf('\Laminas\ServiceManager\AbstractPluginManager', $target);
    }

    public function testConstructorAddsInitializer()
    {
        $target = new AssertionManagerMock($this->serviceManager);

        $this->assertTrue($target->wasAddInitializerCalledCorrectly());
    }

    public function testInjectEventManagerInitializerCallbackDoesNothingIfAssertionNotEventManagerAware()
    {
        $target = new AssertionManager($this->serviceManager);
        $assertion = $this->getMockForAbstractClass('\Laminas\Permissions\Acl\Assertion\AssertionInterface');
        $services = $this->getMockBuilder('\Laminas\ServiceManager\ServiceLocatorInterface')
            ->setMethods(['get', 'has', 'build'])
            ->getMock();
        $services->expects($this->never())->method('get');

        $this->assertNull($target->injectEventManager($assertion, $services));
    }

    public function testInjectEventManagerInitializerCallbackGetsEventManagerFromServicesIfNotSetInAssertion()
    {
        $assertion = $this
            ->getMockForAbstractClass('\AclTest\Assertion\EventManagerAwareAssertionMock')
        ;
        $services = $this->getMockBuilder('\Laminas\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $parentServices = $this
            ->getMockBuilder('\Laminas\ServiceManager\ServiceManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $events = new EventManager();
        $target = new AssertionManager($parentServices);
        $assertion
            ->expects($this->once())
            ->method('getEventManager')
            ->willReturn(null)
        ;
        $assertion
            ->expects($this->once())
            ->method('setEventManager')
            ->with($events)
        ;

        $parentServices
            ->expects($this->once())
            ->method('get')
            ->with('EventManager')
            ->willReturn($events)
        ;
        /*
         * Wanted to use:
         * //$services->expects($this->once())->method('getServiceLocator')->willReturn($parentServices);
         * but PHPUnit does not allow a concrete method in an abstract class to be mocked. :(
         *
         * So I have to do it this way:
         */
        //$services->setServiceLocator($parentServices);

        $this->assertNull($target->injectEventManager($assertion, $services));
    }

    public function testInjectEventManagerInitializerCallbackSetsSharedEventManagerInEventsIfSetInAssertion()
    {
        $target = new AssertionManager($this->serviceManager);
        $assertion = $this->getMockForAbstractClass('\AclTest\Assertion\EventManagerAwareAssertionMock');
        $services = $this->getMockBuilder('\Laminas\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $parentServices = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $events = new EventManager();
        $sharedEvents = new SharedEventManager();

        //$services->setServiceLocator($parentServices);

        $parentServices
            ->expects($this->never())
            ->method('get')
            //->with('SharedEventManager')
            //->willReturn($sharedEvents)
        ;
        $assertion
            ->expects($this->once())
            ->method('getEventManager')
            ->willReturn($events)
        ;

        $this->assertNull($target->injectEventManager($assertion, $services));
        /* @todo setSharedEventManager was removed in AssertionManager.. we need to fix this test. */
        //$this->assertSame($sharedEvents, $events->getSharedManager());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected plugin to be of type Assertion.
     */
    public function testValidateThrowsExceptionIfPluginIsInvalid()
    {
        $target = new AssertionManager($this->serviceManager);
        $assertion = $this->getMockForAbstractClass('\Laminas\Permissions\Acl\Assertion\AssertionInterface');

        $this->assertNull($target->validate($assertion));
        $target->validate(new \stdClass());
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
