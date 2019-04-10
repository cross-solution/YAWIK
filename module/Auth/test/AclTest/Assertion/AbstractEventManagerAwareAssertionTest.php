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

use Acl\Assertion\AbstractEventManagerAwareAssertion;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Tests the AbstractEventManagerAwareAssertion
 *
 * @covers \Acl\Assertion\AbstractEventManagerAwareAssertion
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Acl
 * @group Acl.Assertion
 */
class AbstractEventManagerAwareAssertionTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $target = new TargetMock();

        $this->assertInstanceOf('\Zend\Permissions\Acl\Assertion\AssertionInterface', $target);
        $this->assertInstanceOf('\Zend\EventManager\EventManagerAwareInterface', $target);
    }

    public function testGetEventManagerReturnsNewOrInjectedInstance()
    {
        $target = new TargetMock();
        $events = new EventManager();

        $this->assertInstanceOf('\Zend\EventManager\EventManager', $target->getEventManager());

        $target->setEventManager($events);

        $this->assertSame($events, $target->getEventManager());
    }

    /**
     * @dataProvider provideEventManagerIdentifiers
     *
     * @param $ids
     */
    public function testIdentifiersAreSetOnInjectedEventManager($ids)
    {
        $target = new TargetMock();
        $events = new EventManager();
        $expected = array('Acl\Assertion', 'Acl\Assertion\AbstractEventManagerAwareAssertion', get_class($target), 'Acl/Assertion');

        if (null !== $ids) {
            $target->setEventManagerIdentifiers($ids);
            $expected = $ids + $expected;
        }

        $target->setEventManager($events);

        $this->assertEquals($expected, $events->getIdentifiers());
    }

    public function provideEventManagerIdentifiers()
    {
        return array(
            array(null),
            array(array('test'))
        );
    }

    public function testAssertCallsPreAssertAndTriggersEvent()
    {
        $target = new TargetMock();
        $acl    = new Acl();

        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $events->expects($this->once())
               ->method('triggerUntil')
               ->willReturn(new ResponseCollection());

        $target->setEventManager($events);

        $this->assertTrue($target->assert($acl));
        $target->setPreAssertWillPass();
        $this->assertTrue($target->assert($acl));
        $target->setPreAssertWillFail();
        $this->assertFalse($target->assert($acl));
    }

    public function testAssertReturnsExpectedResult()
    {
        $target = new TargetMock();

        $responseNull = $this->createResponseMock(null);
        $responseEmpty = $this->createResponseMock('');
        $responseZero = $this->createResponseMock(0);
        $responseTrue = $this->createResponseMock(true);
        $responseFalse = $this->createResponseMock(false);

        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $events->expects($this->exactly(5))
               ->method('triggerUntil')
               ->will($this->onConsecutiveCalls($responseNull, $responseEmpty, $responseZero, $responseTrue, $responseFalse));

        $target->setEventManager($events);

        $acl = new Acl();

        $this->assertTrue($target->assert($acl), 'responseNull must return TRUE'); // responseNull
        $this->assertTrue($target->assert($acl), 'responseEmpty must return TRUE'); // responseEmpty
        $this->assertTrue($target->assert($acl), 'responseZero must return TRUE'); // responseZero
        $this->assertTrue($target->assert($acl), 'responseTrue must return TRUE'); // responseTrue
        $this->assertFalse($target->assert($acl), 'responseFalse must return FALSE'); // responseFalse
    }

    protected function createResponseMock($returnValue)
    {
        $response = $this->getMockBuilder('\Zend\EventManager\ResponseCollection')
                             ->disableOriginalConstructor()
                             ->getMock();
        $response->method('last')->willReturn($returnValue);

        return $response;
    }

    public function testPassingArgumentsToEvent()
    {
        $target = new TargetMock();

        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $acl = new Acl();
        $role = new GenericRole('testRole');
        $resource = new GenericResource('testResource');
        $privilege = "doTest";
        $self = $this;

        $events->expects($this->once())->method('triggerUntil')
               ->will($this->returnCallback(function ($callback, $eventName, $event) use ($acl, $role, $resource, $privilege, $self) {
                   $self->assertTrue(is_callable($callback));
                   $self->assertEquals('assert', $eventName);
                   $self->assertSame($acl, $event->getAcl());
                   $self->assertSame($role, $event->getRole());
                   $self->assertSame($resource, $event->getResource());
                   $self->assertSame($privilege, $event->getPrivilege());

                   return new ResponseCollection();
               }));

        $target->setEventManager($events);
        $target->assert($acl, $role, $resource, $privilege);
    }
}

class TargetMock extends AbstractEventManagerAwareAssertion
{
    private $preAssertCalled = false;
    private $preAssertReturns = null;

    public function setEventManagerIdentifiers(array $identifiers)
    {
        $this->identifiers = $identifiers;

        return $this;
    }

    protected function preAssert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $this->preAssertCalled = true;
        parent::preAssert($acl, $role, $resource, $privilege); // for code coverage report.

        return $this->preAssertReturns;
    }

    public function wasPreAssertCalled()
    {
        return $this->preAssertCalled;
    }

    public function setPreAssertWillPass()
    {
        $this->preAssertReturns = true;

        return $this;
    }

    public function setPreAssertWillFail()
    {
        $this->preAssertReturns = false;

        return $this;
    }
}
