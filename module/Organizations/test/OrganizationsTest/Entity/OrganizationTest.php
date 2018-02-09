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


use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\ImageSet;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeePermissions;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\Template;
use Organizations\Entity\WorkflowSettings;


/**
 * Test the organization entity.
 *
 * @covers \Organizations\Entity\Organization
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     * Class under Test
     *
     * @var Organization
     */
    private $target = '\Organizations\Entity\Organization';

    protected $inheritance = [
        '\Core\Entity\AbstractIdentifiableModificationDateAwareEntity',
        '\Organizations\Entity\OrganizationInterface',
        '\Core\Entity\DraftableEntityInterface',
    ];

    public function propertiesProvider()
    {
        $parent = new Organization();
        $permissions = $this->createMock(Permissions::class);
        return [
            [ 'externalId', '1234' ],
            [ 'name', [ 'value'=> '', 'ignore_setter' => true] ],
            [ 'name', [ 'pre' => 'setupGetName', 'value' => 'Test', 'ignore_setter' => true] ],
            [ 'organizationName' , [
                '@value' => [ '\Organizations\Entity\OrganizationName', [ 'Test' ] ],
                'post' => 'organizationNamePostAsserts'
            ]],
            [ 'organizationName', [
                'pre' => 'setupOrganizationName',
                '@value' => [ '\Organizations\Entity\OrganizationName', [ 'TestNew' ] ],
                'post' => 'assertOrganizationNameCounter'
            ]],
            [ 'permissions',[
                'value' => $permissions
            ]],
            [ 'image',[
                'value' => new OrganizationImage()
            ]],
            [ 'images',[
                'value' => new ImageSet(),
            ]],
            [ 'isDraft',[
                'default' => false,
                'value' => true,
                'getter_method' => 'isDraft'
            ]],
            [ 'contact',[
                'value' => new OrganizationContact()
            ]],
            [ 'description',[
                'default' => null,
                'value' => 'Some Description'
            ]],
            [ 'parent',[
                'default' => null,
                'value' => $parent,
            ]],
            [ 'hiringOrganizations',[
                'default' => null,
            ]],

            [ 'employees',[
                '@default' => ArrayCollection::class,
                'value' => new ArrayCollection()
            ]],

            [ 'jobs',[
                'default' => null,
            ]],

            [ 'user',[
                'value' => new User()
            ]],

            [ 'template',[
                'value' => new Template(),
            ]],

            [ 'workflowSettings',[
                'value' => new WorkflowSettings(),
            ]],

            [ 'profileSetting',[
                'value' => Organization::PROFILE_ALWAYS_ENABLE,
                'default' => null,
            ]],

            [ 'hydrator', [
                '@default' => EntityHydrator::class
            ]]

        ];
    }

    public function setupGetName()
    {
        $name = new OrganizationName('Test');
        $this->target->setOrganizationName($name);
    }

    public function organizationNamePostAsserts()
    {
        $this->assertAttributeEquals('Test', '_organizationName', $this->target);
        $this->assertEquals(1, $this->target->getOrganizationName()->getRankingByCompany());
    }

    public function setupOrganizationName()
    {
        $this->orgName = new OrganizationName('Test');
        $this->orgName->setRankingByCompany(11);
        $this->target->setOrganizationName($this->orgName);
        $this->assertEquals(12, $this->orgName->getRankingByCompany());
    }

    public function assertOrganizationNameCounter()
    {
        $this->assertEquals(11, $this->orgName->getRankingByCompany());
    }

    public function testEmployees()
    {
        $user = new User();
        $user->setId('some-id');
        $employee = new Employee();
        $employee
            ->setUser($user)
            ->setRole(Employee::ROLE_RECRUITER)
        ;

        $employees = new ArrayCollection();
        $employees->add($employee);
        $parent = new Organization();
        $organization = new Organization();
        $organization->setEmployees($employees);

        $organization->getEmployee($user);
        $this->assertEquals($employee,$organization->getEmployee($user));
        $this->assertEquals($employee,$organization->getEmployee($user->getId()));
        $this->assertNull($organization->getEmployee('foobar'));
        $this->assertEquals(
            [$employee],
            $organization
                ->getEmployeesByRole(Employee::ROLE_RECRUITER)
                ->toArray()
        );

        $this->assertEquals(1,$organization->getEmployees()->count());
        $organization->setParent($parent);
        $this->assertEquals(
            0,
            $organization->getEmployees()->count(),
            'Should return empty when hiring organization'
        );
    }

    public function testImages()
    {
        $image = new OrganizationImage();
        $images = new ImageSet();
        $images->set(ImageSet::ORIGINAL,$image);

        $organization = new Organization();
        $organization->setImages($images);
        $organization->removeImages();
        $this->assertNotEquals($images,$organization->getImages());
        $this->assertNull($organization->getImages()->get(ImageSet::ORIGINAL));
    }

    public function testGetPermissionsUserId()
    {
        $organization = new Organization();
        $organization->setId('some-id');

        $user = new User();
        $user->setId('some-id');
        $permissions = new EmployeePermissions();
        $employee = new Employee();
        $employee
            ->setUser($user)
            ->setRole(Employee::ROLE_RECRUITER)
        ;
        $employee->setPermissions($permissions);
        $employees = new ArrayCollection([$employee]);
        $organization->setEmployees($employees);
        $organization->setUser($user);


        $this->assertEquals(
            'organization:some-id',
            $organization->getPermissionsResourceId()
        );

        $this->assertEquals(
            ['all'=>['some-id']],
            $organization->getPermissionsUserIds(),
            'Test with null type'
        );

        $employee->getPermissions()->setPermissions(EmployeePermissions::JOBS_VIEW);
        $this->assertEquals(
            [
                'all' => ['some-id'],
                'view' => ['some-id']
            ],
            $organization->getPermissionsUserIds('Job/Permissions')
        );

        $employee->getPermissions()->setPermissions(EmployeePermissions::JOBS_CHANGE);
        $this->assertEquals(
            [
                'all' => ['some-id'],
                'change' => ['some-id']
            ],
            $organization->getPermissionsUserIds('Job/Permissions')
        );

        $employee->getPermissions()->setPermissions(EmployeePermissions::APPLICATIONS_VIEW);
        $this->assertEquals(
            [
                'all' => ['some-id'],
                'view' => ['some-id']
            ],
            $organization->getPermissionsUserIds('Application')
        );

        $employee->getPermissions()->setPermissions(EmployeePermissions::APPLICATIONS_CHANGE);
        $this->assertEquals(
            [
                'all' => ['some-id'],
                'change' => ['some-id']
            ],
            $organization->getPermissionsUserIds('Application')
        );

        $employee->setStatus(Employee::STATUS_UNASSIGNED);
        $this->assertEquals(
            [
                'all' => ['some-id']
            ],
            $organization->getPermissionsUserIds('Application')
        );

    }

    public function testUpdatePermissions()
    {
        $parent = new Organization();
        $parent->setId('parent-id');


        $user = new User();
        $user->setId('some-id');
        $employee = new Employee();
        $employee->setUser($user);
        $employees = new ArrayCollection([$employee]);

        $permissions = $this->createMock(Permissions::class);
        $permissions->expects($this->once())
            ->method('grant')
            ->with($user,PermissionsInterface::PERMISSION_CHANGE,false)
        ;

        $organization = new Organization();
        $organization->setEmployees($employees);
        $organization->setPermissions($permissions);
        $organization->updatePermissions();
    }

//
//    public function setup()
//    {
//        $this->target = new Organization();
//        $owner = new User();
//        $employee1 = new Employee(new User,EmployeePermissionsInterface::APPLICATIONS_CHANGE);
//        $employee2 = new Employee(new User,EmployeePermissionsInterface::APPLICATIONS_CHANGE);
//        $employees = new \Doctrine\Common\Collections\ArrayCollection([$employee1, $employee2]);
//        $this->target->setEmployees($employees);
//    }
//
//    /**
//     * Does the entity implement the correct interface?
//     */
//    public function testOrganizationImplementsInterface()
//    {
//        $this->assertInstanceOf('\Organizations\Entity\OrganizationInterface', $this->target);
//        $this->assertInstanceOf('\Core\Entity\DraftableEntityInterface', $this->target);
//    }
//
//    public function testSetGetParentOrganization()
//    {
//        $parent = new Organization;
//        $this->target->setParent($parent);
//        $this->assertSame($this->target->getParent(),$parent);
//    }
//
//    public function testSetGetHydrator()
//    {
//        $hydrator = new EntityHydrator();
//        $this->target->setHydrator($hydrator);
//        $this->assertEquals($this->target->getHydrator(),$hydrator);
//    }
//
//    public function testSetGetExternalId()
//    {
//        $input="myCompany123";
//        $this->target->setExternalId($input);
//        $this->assertSame($this->target->getExternalId(),$input);
//    }
//
//    public function testSetGetOrganizationNameAndGetName()
//    {
//        $company = 'My Good Company';
//        $input = new OrganizationName($company);
//        $this->target->setOrganizationName($input);
//        $this->assertSame($this->target->getOrganizationName(),$input);
//    }
//
//    public function testGetName()
//    {
//        $company = 'My Good Company';
//        $input = new OrganizationName($company);
//        $this->target->setOrganizationName($input);
//        $this->assertSame($this->target->getName(),$company);
//    }
//
//    public function testGetNameWithoutAName()
//    {
//        $this->assertSame($this->target->getName(),'');
//    }
//
//    public function testSetAnExistingOrganizationName(){
//        $company = 'My Good Company';
//        $input = new OrganizationName($company);
//        $input->setRankingByCompany(2);
//        $input->setId(123);
//        $this->target->setOrganizationName($input);
//        $this->target->setOrganizationName($input);
//        $this->assertSame(3,$input->getRankingByCompany());
//    }
//
//    public function testSetGetDescription()
//    {
//        $description = 'My Good Company Description';
//        $this->target->setDescription($description);
//        $this->assertSame($this->target->getDescription(),$description);
//    }
//
//    public function testSetGetIsDraft()
//    {
//        $input = true;
//        $this->target->setIsDraft($input);
//        $this->assertSame($this->target->isDraft(),$input);
//        $input = false;
//        $this->target->setIsDraft($input);
//        $this->assertSame($this->target->isDraft(),$input);
//    }
//
//    public function testSetGetImage()
//    {
//        $input = new OrganizationImage();
//        $this->target->setImage($input);
//        $this->assertSame($this->target->getImage(),$input);
//    }
//
//    public function testSetGetContact()
//    {
//        $input = new OrganizationContact();
//        $this->target->setContact($input);
//        $this->assertSame($this->target->getContact(),$input);
//    }
//
//    public function testGetPermissionsResourceId()
//    {
//        $input = 1234;
//        $this->target->setId($input);
//        $this->assertSame($this->target->getPermissionsResourceId(),'organization:' . $input);
//    }
//
//    public function testSetGetPermissions(){
//        $permissions = new Permissions(PermissionsInterface::PERMISSION_CHANGE);
//        $this->target->setPermissions($permissions);
//        $this->assertSame($this->target->getPermissions(),$permissions);
//    }
//
//    public function testGetPermissionDefault() {
//        $this->assertEquals(new Permissions(), $this->target->getPermissions());
//    }
//
//    public function testSetGetPermissionsWithUser(){
//        $user = new User();
//        $user->setId(123);
//        $this->target->setUser($user);
//        $permissions = new Permissions(PermissionsInterface::PERMISSION_CHANGE);
//        $this->target->setPermissions($permissions);
//        $this->assertEquals($this->target->getPermissions(), $permissions);
//        $this->assertAttributeSame(true, 'hasChanged', $this->target->getPermissions());
//        $this->assertAttributeSame('change', 'type', $this->target->getPermissions());
//    }
}
