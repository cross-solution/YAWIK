<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Organizations\Entity\EmployeePermissions;
use Organizations\Entity\EmployeePermissionsInterface;

/**
 * Test EmployeePermissions entity.
 *
 * @covers \Organizations\Entity\EmployeePermissions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class EmployeePermissionsTest extends TestCase
{

    /**
     * Is correct interface implemented?
     *
     */
    public function testEmployeePermissionsImplementsInterface()
    {
        $target = new EmployeePermissions();

        $this->assertInstanceOf('Organizations\Entity\EmployeePermissionsInterface', $target);
    }

    /**
     * Is the permission bitmask passed to constructor correctly set?
     *
     * @param int $initial Initial bitmask to set.
     * @param int $expected Expected bitmask if provided, $initial is used, when null
     *
     * @dataProvider providePermissionsValuesForConstructorTest
     */
    public function testSettingInitialPermissionsInConstructor($initial, $expected = null)
    {
        $target = new EmployeePermissions($initial);

        $this->assertEquals($target->getPermissions(), null === $expected ? $initial : $expected);
    }


    /**
     * Are bitmasks correctly set?
     *
     */
    public function testSetPermissionsBitmask()
    {
        $target = new EmployeePermissions();

        $target->setPermissions(8);

        $this->assertEquals($target->getPermissions(), 8);
    }

    /**
     * Does the managing of permissions work?
     *
     * That means, calls to grant, revoke, grantAll and revokeAll.
     *
     */
    public function testManagingPermissions()
    {
        $target = new EmployeePermissions();


        $target->grant(EmployeePermissions::JOBS_CREATE);
        $this->assertTrue($target->isAllowed(EmployeePermissions::JOBS_CREATE));

        $target->revoke(EmployeePermissions::JOBS_CREATE);
        $this->assertFalse($target->isAllowed(EmployeePermissions::JOBS_CREATE));

        // Test setting multiple permissions at once

        $target->setPermissions(18); // reset
        $target->grant(EmployeePermissions::JOBS_CHANGE, EmployeePermissions::APPLICATIONS_CHANGE);
        $this->assertTrue($target->isAllowed(EmployeePermissions::JOBS_CHANGE));
        $this->assertTrue($target->isAllowed(EmployeePermissions::APPLICATIONS_CHANGE));

        $target->revoke(EmployeePermissions::JOBS_CHANGE, EmployeePermissions::APPLICATIONS_CHANGE);
        $this->assertFalse($target->isAllowed(EmployeePermissions::JOBS_CHANGE));
        $this->assertFalse($target->isAllowed(EmployeePermissions::APPLICATIONS_CHANGE));

        // test grantAll / revokeAll

        $target->grantAll();
        $this->assertEquals($target->getPermissions(), EmployeePermissions::ALL);

        $target->revokeAll();
        $this->assertEquals($target->getPermissions(), EmployeePermissions::NONE);
    }

    /**
     * Provides data sets for testSettingInitialPermissionsInConstructor
     *
     * @return array
     */
    public function providePermissionsValuesForConstructorTest()
    {
        return array(
            array(null, EmployeePermissionsInterface::JOBS_VIEW | EmployeePermissions::APPLICATIONS_VIEW),
            array(EmployeePermissionsInterface::JOBS_CREATE),
            array(EmployeePermissionsInterface::APPLICATIONS_CHANGE | EmployeePermissionsInterface::JOBS_VIEW)
        );
    }
}
