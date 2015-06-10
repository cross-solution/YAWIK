<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Acl;

use Applications\Acl\ApplicationAccessAssertion;
use Applications\Entity\Application;
use Auth\Entity\User;
use Core\Entity\PermissionsInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Tests the ApplicationAccessAssertion
 *
 * @covers \Applications\Acl\ApplicationAccessAssertion
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Acl
 */
class ApplicationAccessAssertionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Fixup of the Class under Test
     *
     * @var ApplicationAccessAssertion
     */
    private $target;

    /**
     * Fixup for the ACL needed as first Argument to assert().
     *
     * @var Acl
     */
    private $acl;

    public function setUp()
    {
        $this->target = new ApplicationAccessAssertion();
        $this->acl    = new Acl();
    }
    /**
     * @testdox Implements \Zend\Permsissions\Acl\Assertion\AssertionInterface
     */
    public function testImplementsAssertionInterface()
    {
        $this->assertInstanceOf('\Zend\Permissions\Acl\Assertion\AssertionInterface', $this->target);
    }

    public function provideAssertTestData()
    {
        $role = new GenericRole('Test');
        $resource = new GenericResource('Test');
        $user = new User();
        $user->setId('testuser');
        $user2 = new User();
        $user2->setId('testuser2');
        $app  = new Application();
        $app2 = new Application();
        $app2->getPermissions()->grant($user, PermissionsInterface::PERMISSION_VIEW)
                               ->grant($user2, PermissionsInterface::PERMISSION_CHANGE);

        return array(
            'nouser-noapp'     => array($role, $resource, null, false),
            'user-noapp'       => array($user, $resource, null, false),
            'user-app-no-perm' => array($role, $app, null, false),
            'read-not-granted' => array($user, $app, 'read', false),
            'change-not-granted' => array($user, $app, 'write', false),
            'read-granted' => array($user2, $app2, 'read', true),
            'change-granted' => array($user2, $app2, 'write', true),
            'change-not-granted2' => array($user, $app2, 'change', false),
        );
    }

    /**
     * @dataProvider provideAssertTestData
     *
     * @testdox assert() checks role and resource and resources' permissions if needed
     *
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @param null|string $privilege
     * @param bool $expected
     */
    public function testAssert($role, $resource, $privilege, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->target->assert($this->acl, $role, $resource, $privilege));
        } else {
            $this->assertFalse($this->target->assert($this->acl, $role, $resource, $privilege));
        }
    }

}