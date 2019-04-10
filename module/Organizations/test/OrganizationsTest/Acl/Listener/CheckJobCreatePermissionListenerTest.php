<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */
 
/** */
namespace OrganizationsTest\Acl\Listener;

use PHPUnit\Framework\TestCase;

use Acl\Assertion\AssertionEvent;
use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Organizations\Acl\Listener\CheckJobCreatePermissionListener;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeePermissions;
use Organizations\Entity\EmployeePermissionsInterface;
use Organizations\Entity\OrganizationReference;

/**
 * Tests the CheckJobCreatePermissionListener
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Acl
 * @group Organizations.Acl.Listener
 */
class CheckJobCreatePermissionListenerTest extends TestCase
{
    /**
     * Does the listener attachs itself to the shared event manager?
     * Does it do that on the right event name?
     *
     */
    public function testAttachsToSharedEventManager()
    {
        $target = new CheckJobCreatePermissionListener();

        $events = $this->getMockBuilder('\Zend\EventManager\SharedEventManager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $events->expects($this->once())
               ->method('attach')
               ->with('Jobs/Acl/Assertion/Create', AssertionEvent::EVENT_ASSERT, array($target, 'checkCreatePermission'));

        $target->attachShared($events);
    }

    /**
     * Does detach do nothing? Event returns null?
     *
     */
    public function testDetachIsAnEmptyImplementation()
    {
        $target = new CheckJobCreatePermissionListener();

        $this->assertNull($target->detachShared(new \Zend\EventManager\SharedEventManager()));
    }

    /**
     * Does listener callback returns false if the passed role is no UserInterface?
     *
     */
    public function testCheckReturnsFalseIfRoleIsNoUser()
    {
        $target = new CheckJobCreatePermissionListener();
        $e = new AssertionEvent();
        $e->setRole('noUserInterface');

        $this->assertFalse($target->checkCreatePermission($e));
    }

    /**
     * Does listener callback return true if no organization is set or user is organization owner?
     *
     */
    public function testCheckReturnsTrueIfNoOrganizationOrOwner()
    {
        $target = new CheckJobCreatePermissionListener();

        $org = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')
                    ->disableOriginalConstructor()
                    ->getMock();

        $org->expects($this->exactly(2))
            ->method('hasAssociation')
            ->will($this->onConsecutiveCalls(false, true));

        $org->expects($this->once())
            ->method('isOwner')
            ->willReturn(true);

        $role = new User();
        $role->setOrganization($org);

        $e = new AssertionEvent();
        $e->setRole($role);

        $this->assertTrue($target->checkCreatePermission($e));
        $this->assertTrue($target->checkCreatePermission($e));
    }

    /**
     * Does listener callback returns true if one employer has permission to create jobs?
     *
     */
    public function testCheckReturnsTrueIfOneEmployerIsAllowed()
    {
        $target = new CheckJobCreatePermissionListener();
        $e = $this->getTestEvent();

        $this->assertTrue($target->checkCreatePermission($e));
    }

    /**
     * Does listener callback returns false, if no employer has permissions?
     *
     */
    public function testCheckReturnsFalseIfNoEmployerIsAllowed()
    {
        $target = new CheckJobCreatePermissionListener();
        $e = $this->getTestEvent(false);

        $this->assertFalse($target->checkCreatePermission($e));
    }

    /**
     * Gets an Assertion event seeded with Mock objects.
     *
     * @param bool $isOneEmployeeAllowed if true, one employee in the employees collection gets job create permissions.
     *
     * @return AssertionEvent
     */
    protected function getTestEvent($isOneEmployeeAllowed = true)
    {
        $employees = new ArrayCollection();

        for ($i = 0; $i < 3; $i++) {
            $empUser = new User();
            $empUser->setId('1234-' . $i);
            $perm = new EmployeePermissions(EmployeePermissionsInterface::JOBS_VIEW);
            if (2 == $i && $isOneEmployeeAllowed) {
                $perm->grant(EmployeePermissionsInterface::JOBS_CREATE);
            }
            $emp = new Employee($empUser, $perm);

            $employees->add($emp);
        }

        $org = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')
                    ->disableOriginalConstructor()
                    ->getMock();

        $org->expects($this->once())
            ->method('hasAssociation')
            ->willReturn(true);

        $org->expects($this->once())
            ->method('isOwner')
            ->willReturn(false);

        $org->expects($this->once())
            ->method('getEmployees')
            ->willReturn($employees);

        $role = new User();
        $role->setId('1234-2');
        $role->setOrganization($org);

        $e = new AssertionEvent();
        $e->setRole($role);

        return $e;
    }
}
