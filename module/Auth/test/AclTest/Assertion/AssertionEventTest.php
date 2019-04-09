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

use Acl\Assertion\AssertionEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * test the AssertionEvent
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AssertionEventTest extends TestCase
{
    public function testImplementsInterface()
    {
        $target = new AssertionEvent();

        $this->assertInstanceOf('\Zend\EventManager\EventInterface', $target);
        $this->assertInstanceOf('\Zend\EventManager\Event', $target);
    }

    /**
     * @dataProvider provideSetterAndGetterTestValues
     *
     * @param $func
     * @param $value
     */
    public function testSetterAndGetter($func, $value)
    {
        $target = new AssertionEvent();
        $setter = "set$func";
        $getter = "get$func";

        $this->assertSame($target, $target->$setter($value));
        $this->assertSame($value, $target->$getter());
    }

    public function provideSetterAndGetterTestValues()
    {
        return array(
            array('Acl', new Acl()),
            array('Role', new GenericRole('testRole')),
            array('Resource', new GenericResource('testResource')),
            array('Privilege', 'doTest'),
        );
    }

    public function testDefaultEventName()
    {
        $target = new AssertionEvent();
        $this->assertEquals('assert', AssertionEvent::EVENT_ASSERT);
        $this->assertEquals(AssertionEvent::EVENT_ASSERT, $target->getName());
    }
}
