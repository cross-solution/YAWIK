<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Acl;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Jobs\Acl\CreateAssertion;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Test of Jobs\Acl\CreateAssertion
 *
 * @covers \Jobs\Acl\CreateAssertion
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Acl
 */
class CreateAssertionTest extends TestCase
{

    /**
     * @coversNothing
     */
    public function testExtendsBaseClass()
    {
        $target = new CreateAssertion();

        $this->assertInstanceOf('\Acl\Assertion\AbstractEventManagerAwareAssertion', $target);
    }

    /**
     * @coversNothing
     */
    public function testProvidesDefaultEventManagerIdentifiers()
    {
        $target = new CreateAssertion();
        $expected = array(
            'Jobs/Acl/Assertions',
            'Jobs/Acl/Assertion/Create',
        );

        $this->assertAttributeEquals($expected, 'identifiers', $target);
    }

    public function testPreAssertConditions()
    {
        $target = new CreateAssertionMock();

        $acl = new Acl();
        $role = new GenericRole('test');

        // non user is always false?
        $this->assertFalse($target->assert($acl, $role, null, 'test'));
        $this->assertFalse($target->assert($acl, $role, null, 'new'));

        // user and wrong privilege is false?
        $role = new User();
        $this->assertFalse($target->assert($acl, $role, null, 'test'));

        // user and right privilege is null (meaning triggering event will be done)?
        $this->assertNull($target->assert($acl, $role, null, 'new'));
    }
}

class CreateAssertionMock extends CreateAssertion
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        return parent::preAssert($acl, $role, $resource, $privilege);
    }
}
