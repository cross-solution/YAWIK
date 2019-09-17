<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Acl\Assertion;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use \Organizations\Acl\Assertion\WriteAssertion;
use Organizations\Entity\Organization;
use Zend\Permissions\Acl\Acl;
use \Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Tests for \Organizations\Acl\Assertion\WriteAssertion
 *
 * @covers \Organizations\Acl\Assertion\WriteAssertion
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class WriteAssertionTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = WriteAssertion::class;

    private $inheritance = [ AssertionInterface::class ];

    public function dataProvider()
    {
        $invalidRole = new GenericRole('test');
        $validRole   = new User();
        $validRole->setId('testId');
        $invalidResource = new GenericResource('test');
        $validResourceWoPerms = new Organization();
        $validResourceWPerms = new Organization();
        $validResourceWPerms->setUser($validRole);

        return [
            [ $invalidRole, $invalidResource, 'invalidPrivilege', false ],
            [ $validRole, $invalidResource, 'invalid', false ],
            [ $invalidRole, $validResourceWoPerms, 'invalid', false ],
            [ $invalidRole, $validResourceWPerms, 'invalid', false ],
            [ $invalidRole, $invalidResource, 'edit', false ],
            [ $validRole, $invalidResource, 'edit', false ],
            [ $validRole, $validResourceWPerms, 'invalid', false ],
            [ $validRole, $validResourceWoPerms, 'edit', false ],
            [ $validRole, $validResourceWPerms, 'edit', true ],

        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $role
     * @param $resource
     * @param $privilege
     * @param $expect
     */
    public function testReturnsExpectedResult($role, $resource, $privilege, $expect)
    {
        $acl    = new Acl();
        $result = $this->target->assert($acl, $role, $resource, $privilege);

        if ($expect) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
