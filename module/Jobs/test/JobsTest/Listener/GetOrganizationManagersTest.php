<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Listener;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Listener\Events\AjaxEvent;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Jobs\Listener\GetOrganizationManagers;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeeInterface;
use Organizations\Entity\Organization;
use Organizations\Entity\WorkflowSettings;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Jobs\Listener\GetOrganizationManagers
 *
 * @covers \Jobs\Listener\GetOrganizationManagers
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Listener
 */
class GetOrganizationManagersTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var array|GetOrganizationManagers|null
     */
    private $target = [
        GetOrganizationManagers::class,
        'getTargetConstructorArgs',
        '@testConstruction' => false,
    ];

    /**
     *
     *
     * @var \Organizations\Repository\Organization|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    private function getTargetConstructorArgs()
    {
        $this->repository = $this
            ->getMockBuilder('\Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock()
        ;

        return [$this->repository];
    }

    private function getAjaxEvent($hasOrgId = false)
    {
        $query = new Parameters();

        if ($hasOrgId) {
            $query->set('organization', 'orgid');
        }

        $request = new Request();
        $request->setQuery($query);

        $event = new AjaxEvent();
        $event->setRequest($request);

        return $event;
    }

    /**
     *
     *
     * @param bool|Organization|\PHPUnit_Framework_MockObject_MockObject $org
     */
    private function configureRepository($org = false)
    {
        $this->repository->expects($this->once())->method('find')->with('orgid')->willReturn($org ? $org : null);
    }

    private function createOrganization(array $spec)
    {
        $flag = function ($key) use ($spec) {
            return isset($spec[$key]) && $spec[$key];
        };
        $org = $this
            ->getMockBuilder(Organization::class)
            ->setMethods(['isHiringOrganization', 'getParent', 'getWorkflowSettings', 'getEmployeesByRole'])
            ->getMock();

        if ($flag('isHiringOrg')) {
            $org->expects($this->once())->method('isHiringOrganization')->willReturn(true);
            $org->expects($this->once())->method('getParent')->will($this->returnSelf());
        } else {
            $org->expects($this->once())->method('isHiringOrganization')->willReturn(false);
            $org->expects($this->never())->method('getParent');
        }

        $wf = new WorkflowSettings();
        $wf->setAcceptApplicationByDepartmentManager($flag('acceptApplication'))
           ->setAssignDepartmentManagersToJobs($flag('assignManagers'));

        $org->expects($this->once())->method('getWorkflowSettings')->willReturn($wf);

        if (!$flag('acceptApplication') || !$flag('assignManagers')) {
            return $org;
        }


        $user = new User();
        $user->setId('userid');
        $info = $user->getInfo();
        $info->setFirstName('Test');
        $info->setLastName('User');
        $info->setEmail('test.user@email');

        $employee = new Employee($user);

        $employees = [
            $employee
        ];

        $org->expects($this->once())->method('getEmployeesByRole')->with(EmployeeInterface::ROLE_DEPARTMENT_MANAGER)
            ->willReturn($employees);

        return $org;
    }

    public function testConstruction()
    {
        $repo     = $this->getMockBuilder('\Organizations\Repository\Organization')->disableOriginalConstructor()->getMock();
        $instance = new GetOrganizationManagers($repo);

        $this->assertAttributeSame($repo, 'repository', $instance);
    }

    public function testListenerReturnsMissingOrganizationIdError()
    {
        $event = $this->getAjaxEvent(false);
        $this->repository->expects($this->never())->method('find');

        $actual = $this->target->__invoke($event);

        $this->assertTrue(is_array($actual), 'Listener did not return an array!');
        $this->assertArrayHasKey('error', $actual);
        $this->assertEquals('missing organization id', $actual['error']);
    }

    public function testListenerReturnsNoOrganizationFoundError()
    {
        $event = $this->getAjaxEvent(true);
        $this->configureRepository(false);

        $actual = $this->target->__invoke($event);

        $this->assertTrue(is_array($actual), 'Listener did not return an array!');
        $this->assertArrayHasKey('error', $actual);
        $this->assertEquals('no organization found.', $actual['error']);
    }

    public function testListenerUsesParentOrganization()
    {
        $event = $this->getAjaxEvent(true);
        $this->configureRepository($this->createOrganization(['isHiringOrg' => true, 'acceptApplication' => false]));

        $this->target->__invoke($event);
    }

    public function provideWorkflowFlags()
    {
        return [
            [false, false], [true, false], [false, true]
        ];
    }

    /**
     * @dataProvider provideWorkflowFlags
     *
     * @param bool $acceptApps
     * @param bool $assignManagers
     */
    public function testListenerReturnsDisabledResponse($acceptApps, $assignManagers)
    {
        $event = $this->getAjaxEvent(true);
        $this->configureRepository($this->createOrganization([
                    'acceptApplication' => $acceptApps,
                    'assignManagers' => $assignManagers
        ]));

        $actual = $this->target->__invoke($event);

        $this->assertTrue(is_array($actual), 'Listener did not return an array!');
        $this->assertArrayHasKey('status', $actual);
        $this->assertEquals('disabled', $actual['status']);
    }

    public function testListenerReturnsManagerList()
    {
        $event = $this->getAjaxEvent(true);
        $this->configureRepository($this->createOrganization([
            'acceptApplication' => true,
            'assignManagers'    => true,
        ]));

        $actual = $this->target->__invoke($event);
        $expect = [
            'status' => 'ok',
            'managers' => [
                [
                    'id' => 'userid',
                    'name' => 'Test User',
                    'email' => 'test.user@email'
                ]
            ]
        ];

        $this->assertEquals($expect, $actual);
    }
}
