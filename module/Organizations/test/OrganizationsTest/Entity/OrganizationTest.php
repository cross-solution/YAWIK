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
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeePermissionsInterface;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationContact;


/**
 * Test the organization entity.
 *
 * @covers Organizations\Entity\Organization
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait, SetterGetterTrait;



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

    protected $properties = [
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
        ]]

    ];

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