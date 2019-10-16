<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Exception\MissingDependencyException;
use Organizations\Controller\Plugin\AcceptInvitationHandler;
use Organizations\Entity\Employee;
use Organizations\Entity\Organization;

/**
 * Tests for \Organizations\Controller\Plugin\AcceptInvitationHandler
 *
 * @covers \Organizations\Controller\Plugin\AcceptInvitationHandler
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Controller
 * @group Organizations.Controller.Plugin
 */
class AcceptInvitationHandlerTest extends TestCase
{
    private $target;
    private $authMock;
    private $organizationRepositoryMock;
    private $userRepositoryMock;
    private $userMock;
    private $organizationMock;

    protected function setUp(): void
    {
        $name = $this->getName(false);

        $this->target = new AcceptInvitationHandler();

        if (false !== strpos($name, 'Exception')) {
            return;
        }

        $this->authMock = $this->getMockBuilder('\Auth\AuthenticationService')->disableOriginalConstructor()->getMock();
        $this->organizationRepositoryMock = $this->getMockBuilder('Organizations\Repository\Organization')
                                                 ->disableOriginalConstructor()->getMock();
        $this->userRepositoryMock = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();

        if (false !== strpos($this->getName(false), 'Setter')) {
            return;
        }

        $this->target->setAuthenticationService($this->authMock);

        $this->target->setOrganizationRepository($this->organizationRepositoryMock);
        if (false !== strpos($this->getName(false), 'OrganizationNotFound')) {
            return;
        }
        $this->organizationMock = $this->getMockBuilder('\Organizations\Entity\Organization')->getMock();

        $this->organizationRepositoryMock->expects($this->once())->method('find')->with('testOrgId')->willReturn($this->organizationMock);

        $this->target->setUserRepository($this->userRepositoryMock);

        if (false !== strpos($this->getName(false), 'TokenIsInvalid')) {
            return;
        }

        $this->userMock = new User();
        $this->userMock->setId('testUserId');
        $employee = $this->getMockBuilder('\Organizations\Entity\Employee')->getMock();
        $employee->expects($this->once())->method('setStatus')->with(Employee::STATUS_ASSIGNED);

        $this->organizationMock->expects($this->once())->method('getEmployee')->with($this->userMock->getId())
                               ->willReturn($employee);

        $this->organizationMock->expects($this->any())->method('getId')->willReturn('testOrgId');
        $this->userRepositoryMock->expects($this->once())->method('findByToken')->with('testToken')->willReturn($this->userMock);

        $sameOrganization = new Organization();
        $sameOrganization->setId('testOrgId');

        $assignedEmp = $this->getMockBuilder('\Organizations\Entity\Employee')->getMock();
        $assignedEmp->expects($this->once())->method('isUnassigned')->with(true)->willReturn(false);
        $assignedEmp->expects($this->once())->method('setStatus')->with(Employee::STATUS_REJECTED);

        $assignedEmpOrganization = $this->getMockBuilder('\Organizations\Entity\Organization')->getMock();
        $assignedEmpOrganization->expects($this->once())->method('getId')->willReturn('otherId');
        $assignedEmpOrganization->expects($this->once())->method('getEmployee')->with($this->userMock->getId())
                                ->willReturn($assignedEmp);

        $unassignedEmp = $this->getMockBuilder('\Organizations\Entity\Employee')->getMock();
        $unassignedEmp->expects($this->once())->method('isUnassigned')->with(true)->willReturn(true);
        $unassignedEmp->expects($this->never())->method('setStatus');

        $unassignedEmpOrganization = $this->getMockBuilder('\Organizations\Entity\Organization')->getMock();
        $unassignedEmpOrganization->expects($this->once())->method('getId')->willReturn('otherId');
        $unassignedEmpOrganization->expects($this->once())->method('getEmployee')->with($this->userMock->getId())
                                ->willReturn($unassignedEmp);

        $this->organizationRepositoryMock->expects($this->once())
                                         ->method('findPendingOrganizationsByEmployee')
                                         ->with($this->userMock->getId())
                                         ->willReturn(array($sameOrganization, $assignedEmpOrganization, $unassignedEmpOrganization));

        $storageMock = $this->getMockForAbstractClass('\Zend\Authentication\Storage\StorageInterface');
        $storageMock->expects($this->once())->method('write')->with($this->userMock->getId());
        $this->authMock->expects($this->once())->method('getStorage')->willReturn($storageMock);
    }

    public function testSetterAndGetter()
    {
        $this->assertSame($this->target, $this->target->setAuthenticationService($this->authMock), 'Fluent interface broken: setAuthenticationService()');
        $this->assertSame($this->target, $this->target->setOrganizationRepository($this->organizationRepositoryMock), 'Fluent interface broken: setOrganizationRepository()');
        $this->assertSame($this->target, $this->target->setUserRepository($this->userRepositoryMock), 'Fluent interface broken: setUserRepository()');

        $this->assertSame($this->organizationRepositoryMock, $this->target->getOrganizationRepository());
        $this->assertSame($this->userRepositoryMock, $this->target->getUserRepository());
        $this->assertSame($this->authMock, $this->target->getAuthenticationService());
    }

    public function testGetterThrowException()
    {
        foreach (array('getAuthenticationService', 'getOrganizationRepository', 'getUserRepository') as $method) {
            try {
                $this->target->$method();
            } catch (MissingDependencyException $e) {
                self::assertTrue(true);
                continue;
            }

            $this->fail('Expected exception was not thrown for "' . $method . '"');
        }
    }


    public function testReturnsErrorIndicatingOrganizationNotFound()
    {
        $orgid = 'testOrgId';
        $this->organizationRepositoryMock->expects($this->once())->method('find')->with($orgid)->willReturn(null);

        $result = $this->target->process('testToken', $orgid);

        $this->assertEquals(AcceptInvitationHandler::ERROR_ORGANIZATION_NOT_FOUND, $result);
    }

    public function testReturnsErrorIndicatinTokenIsInvalidOrExpired()
    {
        $token = 'testToken';
        $this->userRepositoryMock->expects($this->once())->method('findByToken')->with($token)->willReturn(null);

        $result = $this->target->process($token, 'testOrgId');

        $this->assertEquals(AcceptInvitationHandler::ERROR_TOKEN_INVALID, $result);
    }

    public function testUserDraftsWillBeActivated()
    {
        $this->userMock->setIsDraft(true);

        $result = $this->target->process('testToken', 'testOrgId');

        $this->assertEquals(AcceptInvitationHandler::OK_SET_PW, $result);
        $this->assertFalse($this->userMock->isDraft());
    }

    public function testAssigendUsersWillBeUnassigned()
    {
        $empMock = $this->getMockBuilder('\Organizations\Entity\Employee')->getMock();
        $empMock->expects($this->once())->method('setStatus')->with(Employee::STATUS_UNASSIGNED);

        $orgRef = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')->disableOriginalConstructor()->getMock();
        $orgRef->expects($this->once())->method('hasAssociation')->willReturn(true);
        $orgRef->expects($this->once())->method('getEmployee')->with($this->userMock->getId())
               ->willReturn($empMock);

        $this->userMock->setOrganization($orgRef);

        $result = $this->target->process('testToken', 'testOrgId');

        $this->assertEquals(AcceptInvitationHandler::OK, $result);
    }

    public function testUnAssigendUsersWillRemainUnassigned()
    {
        $empMock = $this->getMockBuilder('\Organizations\Entity\Employee')->getMock();
        $empMock->expects($this->never())->method('setStatus');

        $orgRef = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')->disableOriginalConstructor()->getMock();
        $orgRef->expects($this->once())->method('hasAssociation')->willReturn(false);
        $orgRef->expects($this->never())->method('getEmployee');

        $this->userMock->setOrganization($orgRef);

        $result = $this->target->process('testToken', 'testOrgId');

        $this->assertEquals(AcceptInvitationHandler::OK, $result);
    }
}
