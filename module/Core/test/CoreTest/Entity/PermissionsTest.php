<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;

/**
 * Test the Permissions Entity
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\Permissions
 */
class PermissionsTest extends TestCase
{

    /*
     * General tests
     */

    /**
     * Tests if Permissions implements the correct interface
     *
     */
    public function testEntityImplementsInterface()
    {
        $target = new Permissions();

        $this->assertInstanceOf('\Core\Entity\PermissionsInterface', $target);
    }

    /**
     * Tests setting permissions type via constructor.
     *
     */
    public function testTypeIssetViaConstructor()
    {
        $target = new Permissions();

        $this->assertAttributeEquals(get_class($target), 'type', $target);

        $target = new Permissions('testTypeSet');

        $this->assertAttributeEquals('testTypeSet', 'type', $target);
    }

    /**
     * Tests if cloning a Permissions entity creates new instance of the collection of resources.
     *
     */
    public function testCloneCreatesNewResourceCollection()
    {
        $resource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                         ->getMockForAbstractClass();

        $target1 = new Permissions();
        $target1->grant($resource, Permissions::PERMISSION_ALL);

        $coll1 = $target1->getResources();

        $target2 = clone $target1;
        $coll2   = $target2->getResources();

        $this->assertNotSame($coll1, $coll2);
        $this->assertEquals($coll1, $coll2);
    }

    /*
     * __call Magic methods tests.
     */

    /**
     * Tests if Magic Methods throws expected exception when called without arguments.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Missing required parameter.
     */
    public function testMagicMethodsThrowsExceptionsIfCalledWithoutArguments()
    {
        $target = new Permissions();

        $target->grantAll();
    }

    /**
     * Tests if Magic Methods throws expected exception when unknown method is called.
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Unknown method
     */
    public function testMagicMethodsThrowsExceptionsIfCalledWithUnknownMethodName()
    {
        $target = new Permissions();

        $target->unknownMethod('dummyUser');
    }

    /**
     * Tests if Magic Methods calls correct concrete method.
     *
     */
    public function testMagicMethodsCallsProxiesToCorrectMethods()
    {
        $user         = 'dummyUser';
        $viewParams   = array($user, PermissionsInterface::PERMISSION_VIEW);
        $changeParams = array($user, PermissionsInterface::PERMISSION_CHANGE);
        $noneParams   = array($user, PermissionsInterface::PERMISSION_NONE);
        $allParams    = array($user, PermissionsInterface::PERMISSION_ALL);

        $target = $this->getMockBuilder('\Core\Entity\Permissions')->disableOriginalConstructor()
                       ->setMethods(array('isGranted', 'grant', 'revoke'))->getMock();


        $target->expects($this->exactly(4))
               ->method('isGranted')
               ->withConsecutive($viewParams, $changeParams, $noneParams, $allParams)
               ->willReturn(null);

        $target->expects($this->exactly(4))
               ->method('grant')
               ->withConsecutive($viewParams, $changeParams, $noneParams, $allParams)
               ->willReturn(null);

        $target->expects($this->exactly(4))
               ->method('revoke')
               ->withConsecutive($viewParams, $changeParams, $noneParams, $allParams)
               ->willReturn(null);

        /* Test starts here */

        foreach (array('View', 'Change', 'None', 'All') as $perm) {
            $isMethod     = "is{$perm}Granted";
            $grantMethod  = "grant{$perm}";
            $revokeMethod = "revoke{$perm}";

            $target->$isMethod($user);
            $target->$grantMethod($user);
            $target->$revokeMethod($user);
        }
    }


    /*
     * Test function grant()
     */

    /**
     * Tests if permissions are set correctly.
     *
     */
    public function testGrantPermissionsToUsers()
    {
        $resource = 'testUser';
        $target   = new Permissions();

        foreach (array(
                     PermissionsInterface::PERMISSION_VIEW   => array(true, false, false, false),
                     PermissionsInterface::PERMISSION_CHANGE => array(true, true, true, false),
                     PermissionsInterface::PERMISSION_ALL    => array(true, true, true, false),
                     PermissionsInterface::PERMISSION_NONE   => array(false, false, false, true),
                 ) as $perm => $expected
        ) {
            $target->grant($resource, $perm);
            $this->assertEquals($expected[0], $target->isGranted($resource, PermissionsInterface::PERMISSION_VIEW));
            $this->assertEquals($expected[1], $target->isGranted($resource, PermissionsInterface::PERMISSION_CHANGE));
            $this->assertEquals($expected[2], $target->isGranted($resource, PermissionsInterface::PERMISSION_ALL));
            $this->assertEquals($expected[3], $target->isGranted($resource, PermissionsInterface::PERMISSION_NONE));
        }
    }

    public function provideRolePermissionsData()
    {
        return [
            [ 'user', 'user', false, true ],
            [ 'recruiter', 'user', false, false ],
            [ 'all', 'irrelevant', false, true ],
            [ 'user', 'user', true, true ],
            [ 'recruiter', 'user', true, false],
            [ 'all', 'irrelevant', true, true ],
            [ 'testUser', null, true, true ],
            [ 'anonymous', null, true, false ],
        ];
    }

    /**
     * @dataProvider provideRolePermissionsData
     */
    public function testGrantPermissionsToRoles($grantRole, $testRole, $useUserEntity, $expect)
    {
        $target = new Permissions();
        $target->grant($grantRole, PermissionsInterface::PERMISSION_VIEW);

        if ($useUserEntity) {
            $user = new User();
            $user->setId('testUser');
            $user->setRole($testRole);
        } else {
            $user = $testRole;
        }

        $this->assertEquals($expect, $target->isGranted($user, PermissionsInterface::PERMISSION_VIEW));
    }

    /**
     * Tests if the building of Permissions is defered when requested.
     *
     */
    public function testGrantDefersBuild()
    {
        $target = new Permissions();

        $target->grant('testUser', PermissionsInterface::PERMISSION_ALL, false);

        $this->assertFalse($target->isGranted('testUser', PermissionsInterface::PERMISSION_ALL));
        $target->build();
        $this->assertTrue($target->isGranted('testUser', PermissionsInterface::PERMISSION_ALL));
    }

    /**
     * Tests if the previously set permission is used when TRUE is passed as permission.
     *
     */
    public function testGrantUsesAssigendPermissionIfTrueIsPassed()
    {
        $target = new Permissions();

        $user = new User();
        $user->setId('testUser');

        $target->grant($user, PermissionsInterface::PERMISSION_VIEW);
        $target->grant($user, true);

        $this->assertTrue($target->isGranted($user, PermissionsInterface::PERMISSION_VIEW));
        $this->assertFalse($target->isGranted($user, PermissionsInterface::PERMISSION_CHANGE));
    }

    /**
     * Tests if grant traverses the resources when they are given as an array.
     *
     */
    public function testGrantTraversesIfResourceIsAnArray()
    {
        $user1 = new User();
        $user1->setId('user1');
        $user2 = new User();
        $user2->setId('user2');

        $resource = array($user1, $user2);

        $permission = PermissionsInterface::PERMISSION_ALL;

        $target = new Permissions();

        $target->grant($resource, $permission);

        $this->assertTrue($target->isGranted($user1, PermissionsInterface::PERMISSION_ALL));
        $this->assertTrue($target->isGranted($user2, PermissionsInterface::PERMISSION_ALL));

        $target = new Permissions();

        $target->grant($resource, PermissionsInterface::PERMISSION_VIEW, false);

        $this->assertFalse(
            $target->isGranted($user1, PermissionsInterface::PERMISSION_VIEW),
            'Disable build of permissions test failed!'
        );
    }

    /**
     * Tests if the expected exception is thrown if an invalid permission is passed.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid permission.
     */
    public function testGrantThrowsExceptionIfInvalidPermissionIsPassed()
    {
        $target = new Permissions();

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Invalid permission.');

        $target->grant('test', 'invalidPermission');
    }

    /**
     * Tests using a resource object that implements PermissionsResourceInterface.
     *
     */
    public function testGrantWorksWhenPassingPermissionsResourceInterfaces()
    {
        $resource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                         ->getMockForAbstractClass();

        $resource->expects($this->any())
                 ->method('getPermissionsResourceId')->willReturn('resource');

        $userIds1 = array('user1', 'user2');
        $userIds2 = array('view' => array('user1', 'user2'), 'all' => array('user3', 'user4'));
        $resource->expects($this->exactly(4))
                 ->method('getPermissionsUserIds')
                 ->will($this->onConsecutiveCalls(
                     $userIds1,
                     $userIds2,
                     array(),
                     null
                        ));


        $target = new Permissions();

        $target->grant($resource, Permissions::PERMISSION_NONE);

        $this->assertEquals(0, $target->getResources()->count());

        $target->grant($resource, Permissions::PERMISSION_VIEW);

        $coll = $target->getResources();

        $this->assertSame($resource, $coll->current());
        $this->assertEquals(array('resource' => array('view' => $userIds1)), $target->getAssigned());

        $target->grant($resource);

        $this->assertEquals(array('resource' => $userIds2), $target->getAssigned());

        $target->grant($resource, Permissions::PERMISSION_ALL);

        $this->assertEquals(array('resource' => array()), $target->getAssigned());

        $target->grant($resource, Permissions::PERMISSION_CHANGE);

        $this->assertEquals(array('resource' => array()), $target->getAssigned());

        $target->grant($resource, Permissions::PERMISSION_NONE);

        $this->assertEquals(0, $target->getResources()->count());
    }

    /*
     * Test function revoke()
     */

    /**
     * Data provider for testRevoke()
     *
     * @return array
     */
    public function revokeTestProvider()
    {
        return array(
            array(false, PermissionsInterface::PERMISSION_NONE, true),
            array(false, PermissionsInterface::PERMISSION_VIEW, true),
            array(true, PermissionsInterface::PERMISSION_CHANGE, false),
            array(true, PermissionsInterface::PERMISSION_ALL, true),
        );
    }

    /**
     * Tests revoking permissions.
     *
     * @dataProvider revokeTestProvider
     *
     * @param boolean $shouldBeAssigned Should the test assume, that the user was granted a permission prior to revoking it.
     * @param string  $perm             The permission to revoke
     * @param boolean $build            Passed as third paramter to revoke.
     */
    public function testRevoke($shouldBeAssigned, $perm, $build)
    {
        $resource = 'testUser';

        /* @var $target \Core\Entity\PermissionsInterface|\PHPUnit_Framework_MockObject_MockObject */
        $target = $this->getMockBuilder('\Core\Entity\Permissions')
                       ->disableOriginalConstructor()
                       ->setMethods(array('grant', 'isAssigned'))
                       ->getMock();

        if (PermissionsInterface::PERMISSION_NONE == $perm) {
            $target->expects($this->never())->method('isAssigned');
            $target->expects($this->never())->method('grant');
        } elseif (!$shouldBeAssigned) {
            $target->expects($this->once())->method('isAssigned')->with($resource)->willReturn(false);
            $target->expects($this->never())->method('grant');
        } else {
            $target->expects($this->once())->method('isAssigned')->with($resource)->willReturn(true);
            $expPerm = PermissionsInterface::PERMISSION_CHANGE == $perm
                ? PermissionsInterface::PERMISSION_VIEW
                : PermissionsInterface::PERMISSION_NONE;

            $target->expects($this->once())->method('grant')->with($resource, $expPerm, $build)
                   ->will($this->returnSelf());
        }

        /* Test start here */

        $this->assertSame($target, $target->revoke($resource, $perm, $build));
    }

    /*
     * Test isGranted()
     */

    /**
     * Data provider for testIsGranted
     *
     * @return array
     */
    public function isGrantedTestProvider()
    {
        $user = new User();
        $user->setId('testUser');

        return array(
            array('testUser', false, array(true, false, false, false)),
            array('testUser', PermissionsInterface::PERMISSION_VIEW, array(false, true, false, false)),
            array('testUser', PermissionsInterface::PERMISSION_CHANGE, array(false, true, true, true)),
            array('testUser', PermissionsInterface::PERMISSION_ALL, array(false, true, true, true, true)),
            array($user, false, array(true, false, false, false)),
            array($user, PermissionsInterface::PERMISSION_VIEW, array(false, true, false, false)),
            array($user, PermissionsInterface::PERMISSION_CHANGE, array(false, true, true, true)),
            array($user, PermissionsInterface::PERMISSION_ALL, array(false, true, true, true, true)),

        );
    }

    /**
     * Tests if isGranted returns the expected results.
     *
     * @dataProvider isGrantedTestProvider
     *
     * @param string|User $user
     * @param string      $preGrant permission to grant the user prior to testing
     * @param boolean[]   $expected array with for elements which are the expected return values for
     *                              isGranted with the provided user who is granted the $preGrant permission for
     *                              all possible Permissions (ALL, NONE, VIEW, CHANGE).
     */
    public function testIsGranted($user, $preGrant, $expected)
    {
        $target = new Permissions();

        if (false !== $preGrant) {
            $target->grant($user, $preGrant);
        }

        foreach (array(
                     PermissionsInterface::PERMISSION_NONE,
                     PermissionsInterface::PERMISSION_VIEW,
                     PermissionsInterface::PERMISSION_CHANGE,
                     PermissionsInterface::PERMISSION_ALL
                 ) as $i => $perm
        ) {
            $this->assertEquals($expected[$i], $target->isGranted($user, $perm));
        }
    }

    /*
     * Test clear()
     */

    /**
     * tests clearing the Permissions entity.
     *
     */
    public function testClear()
    {
        $resource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                         ->getMockForAbstractClass();

        $resource->expects($this->any())
                 ->method('getPermissionsResourceId')->willReturn('resource');

        $resource->expects($this->exactly(1))->method('getPermissionsUserIds')
                 ->willReturn(array('test'));

        $target = new Permissions();
        $target->grant('test', Permissions::PERMISSION_ALL);
        $target->grant($resource, Permissions::PERMISSION_ALL);

        $expectAssigned = array(
            'user:test' => array('all' => array('test')),
            'resource'  => array('all' => array('test')),
        );
        $this->assertAttributeEquals(array('test'), 'view', $target);
        $this->assertAttributeEquals(array('test'), 'change', $target);
        $this->assertAttributeEquals($expectAssigned, 'assigned', $target);
        $this->assertAttributeInstanceOf('\Core\Entity\Collection\ArrayCollection', 'resources', $target);

        $target->clear();

        $this->assertAttributeEquals(array(), 'view', $target);
        $this->assertAttributeEquals(array(), 'change', $target);
        $this->assertAttributeEquals(array(), 'assigned', $target);
        $this->assertAttributeEquals(null, 'resources', $target);
    }

    /*
     * Test inherit()
     */

    /**
     * Tests if inheriting Permissions works as expected.
     *
     */
    public function testInherit()
    {
        $resource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                         ->getMockForAbstractClass();

        $resource->expects($this->any())
                 ->method('getPermissionsResourceId')->willReturn('resource');

        $resource->expects($this->any())->method('getPermissionsUserIds')
                 ->willReturn(array('test'));

        $target1 = new Permissions();
        $target2 = new Permissions();

        $target1->grant('userTarget1', Permissions::PERMISSION_ALL);
        $target1->grant($resource, Permissions::PERMISSION_VIEW);
        $target2->grant('user', Permissions::PERMISSION_VIEW);

        $target2->inherit($target1);

        $this->assertTrue($target2->isGranted('userTarget1', Permissions::PERMISSION_ALL));
        $this->assertTrue($target2->isGranted('test', Permissions::PERMISSION_VIEW));
    }

    /*
     * test isAssigned()
     */

    /**
     * Tests if isAssigned returns the expected results.
     *
     */
    public function testIsAssigned()
    {
        $target = new Permissions();

        $target->grant('tset', Permissions::PERMISSION_ALL);

        $this->assertFalse($target->isAssigned('test'));
        $this->assertTrue($target->isAssigned('tset'));
    }

    /*
     * test hasChanged()
     */

    /**
     * Tests if hasChanged returns correct values.
     *
     */
    public function testHasChangedReturnsTrueIfPermissionsChangedAndFalseIfNot()
    {
        $target = new Permissions();

        $this->assertFalse($target->hasChanged());
        $target->grant('testUser', PermissionsInterface::PERMISSION_ALL);

        $this->assertTrue($target->hasChanged(), 'Changed-Flag did not return true after change.');
    }

    /*
     * test Getter (getAssigned() and getResources() and getFrom()
     */

    /**
     * Tests if getAssigned returns the correct value.
     *
     */
    public function testGetAssignedReturnsSpecificationArray()
    {
        $target = new Permissions();
        $target->grantAll('testUser');
        $user = new User();
        $user->setId('testUser2');
        $target->grantView($user);

        $expected = array(
            'user:testUser'  => array(PermissionsInterface::PERMISSION_ALL => array('testUser')),
            'user:testUser2' => array(PermissionsInterface::PERMISSION_VIEW => array('testUser2')),
        );

        $this->assertEquals($expected, $target->getAssigned());
    }

    /**
     * Tests if getResource returns empty ArrayCollection when no resources are assigned.
     *
     */
    public function testGetResourcesReturnsEmptyArrayCollection()
    {
        $target = new Permissions();

        $coll = $target->getResources();

        $this->assertInstanceOf('\Core\Entity\Collection\ArrayCollection', $coll);
        $this->assertEquals(0, $coll->count());
    }

    /**
     * Tests if getResources returns an ArrayCollection with the correct resources contained in.
     *
     */
    public function testGetResourcesReturnsCollectionOfResources()
    {
        $resource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                         ->getMockForAbstractClass();

        $target = new Permissions();
        $target->grant($resource, PermissionsInterface::PERMISSION_ALL);

        $coll = $target->getResources();

        $this->assertEquals(1, $coll->count());
        $this->assertSame($resource, $coll->current());
    }

    /**
     * Tests if getFrom returns correct value.
     *
     */
    public function testGetFromReturnsCorrectPermission()
    {
        $grantedResource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                                ->getMockForAbstractClass();

        $grantedResource->expects($this->atLeastOnce())
                        ->method('getPermissionsUserIds')
                        ->will($this->onConsecutiveCalls(array(
                                                             'all'  => array('user1', 'user2'),
                                                             'view' => array('popel', 'dopel'),
                                                         ), array('all' => array('user3'))));
        $grantedResource->method('getPermissionsResourceId')->willReturn('grantResource');

        $ungrantedResource = $this->getMockBuilder('\Core\Entity\PermissionsResourceInterface')
                                  ->getMockForAbstractClass();
        $ungrantedResource->method('getPermissionsResourceId')->willReturn('ungrant');

        $target = new Permissions();

        $target->grant($grantedResource, PermissionsInterface::PERMISSION_ALL);

        $this->assertEquals(PermissionsInterface::PERMISSION_NONE, $target->getFrom($ungrantedResource));
        $this->assertEquals(null, $target->getFrom($grantedResource));

        $target->grant($grantedResource, PermissionsInterface::PERMISSION_CHANGE);

        $this->assertEquals(PermissionsInterface::PERMISSION_ALL, $target->getFrom($grantedResource));
    }
}
