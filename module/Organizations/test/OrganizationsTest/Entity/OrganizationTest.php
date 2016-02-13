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
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
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

    /**
     * Class under Test
     *
     * @var Organization
     */
    private $target;

    public function setup()
    {
        $this->target = new Organization();
        $owner = new User();
        $employee1 = new Employee(new User,EmployeePermissionsInterface::APPLICATIONS_CHANGE);
        $employee2 = new Employee(new User,EmployeePermissionsInterface::APPLICATIONS_CHANGE);
        $employees = new \Doctrine\Common\Collections\ArrayCollection([$employee1, $employee2]);
        $this->target->setEmployees($employees);
    }

    /**
     * Does the entity implement the correct interface?
     */
    public function testOrganizationImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\OrganizationInterface', $this->target);
        $this->assertInstanceOf('\Core\Entity\DraftableEntityInterface', $this->target);
    }

    public function testSetGetParentOrganization()
    {
        $parent = new Organization;
        $this->target->setParent($parent);
        $this->assertSame($this->target->getParent(),$parent);
    }

    public function testGetHiringOrganizations()
    {
        /*@todo*/
    }

    public function testSetGetExternalId()
    {
        $input="myCompany123";
        $this->target->setExternalId($input);
        $this->assertSame($this->target->getExternalId(),$input);
    }

    public function testSetGetOrganizationNameAndGetName()
    {
        $company = 'My Good Company';
        $input = new OrganizationName($company);
        $this->target->setOrganizationName($input);
        $this->assertSame($this->target->getOrganizationName(),$input);
    }

    public function testGetName()
    {
        $company = 'My Good Company';
        $input = new OrganizationName($company);
        $this->target->setOrganizationName($input);
        $this->assertSame($this->target->getName(),$company);
    }

    public function testSetGetDescription()
    {
        $description = 'My Good Company Description';
        $this->target->setDescription($description);
        $this->assertSame($this->target->getDescription(),$description);
    }

    public function testSetGetIsDraft()
    {
        $input = true;
        $this->target->setIsDraft($input);
        $this->assertSame($this->target->isDraft(),$input);
        $input = false;
        $this->target->setIsDraft($input);
        $this->assertSame($this->target->isDraft(),$input);
    }

    public function testSetGetImage()
    {
        $input = new OrganizationImage();
        $this->target->setImage($input);
        $this->assertSame($this->target->getImage(),$input);
    }

    public function testSetGetContact()
    {
        $input = new OrganizationContact();
        $this->target->setContact($input);
        $this->assertSame($this->target->getContact(),$input);
    }

    public function testGetPermissionsResourceId()
    {
        $input = 1234;
        $this->target->setId($input);
        $this->assertSame($this->target->getPermissionsResourceId(),'organization:' . $input);
    }

    public function testSetGetPermissions(){
        $permissions = new Permissions(PermissionsInterface::PERMISSION_CHANGE);
        $this->target->setPermissions($permissions);
        $this->assertSame($this->target->getPermissions(),$permissions);
    }
}