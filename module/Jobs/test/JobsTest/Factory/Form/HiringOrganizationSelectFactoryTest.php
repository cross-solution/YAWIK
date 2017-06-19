<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Form;

use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Interop\Container\ContainerInterface;
use Jobs\Factory\Form\HiringOrganizationSelectFactory;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationName;
use Zend\Form\FormElementManager;

/**
 * Tests for the HiringOrganizationSelect factory
 *
 * @covers \Jobs\Factory\Form\HiringOrganizationSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Form
 */
class HiringOrganizationSelectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var HiringOrganizationSelectFactory
     */
    private $target;

    /**
     * The user entity fixture
     *
     * @var User
     */
    private $user;

    /**
     * The form element manager mock
     *
     * @var FormElementManager
     */
    private $formElements;
	
	/**
	 * @var ContainerInterface
	 */
    private $services;

    public function setUp()
    {
        $this->target = new HiringOrganizationSelectFactory();

        if ("testImplementsFactoryInterface" == $this->getName(/*withDataSet */ false)) {
            return;
        }

        $userOrg = $this->getMockBuilder('\Organizations\Entity\OrganizationReference')
                        ->disableOriginalConstructor()
                        ->getMock();


        $user = new User();
        $user->setId('testUser');
        $user->setOrganization($userOrg);

        $this->user = $user;

        $auth = $this->getMockBuilder('Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $auth->expects($this->once())
             ->method('getUser')
             ->willReturn($user);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())
                 ->method('get')
                 ->with('AuthenticationService')
                 ->willReturn($auth);
	    
        $this->services = $services;
    }

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $this->target);
    }

    /**
     * @testdox createService() returns select form element.
     */
    public function testCreateServiceReturnsSelectFormElement()
    {
        /* @var $org \PHPUnit_Framework_MockObject_MockObject */
        $org = $this->user->getOrganization();
        $org->expects($this->once())->method('hasAssociation')->willReturn(false);

        $select = $this->target->__invoke($this->services,'irrelevant');

        $this->assertInstanceOf('\Jobs\Form\HiringOrganizationSelect', $select);
    }
    /**
     * @testdox createService() returns select element with no value options if no organization is associated to the user.
     */
    public function testCreateServiceWithNoAssociation()
    {
        /* @var $org \PHPUnit_Framework_MockObject_MockObject */
        $org = $this->user->getOrganization();
        $org->expects($this->once())->method('hasAssociation')->willReturn(false);

        $select = $this->target->__invoke($this->services,'irrelevant');

        $this->assertEquals(array(), $select->getValueOptions());
    }

    /**
     * @testdox createService() returns select element with value options if organization is associated to the user.
     */
    public function testCreateServiceWithAssociation()
    {
        /* @var $org \PHPUnit_Framework_MockObject_MockObject */
        $org = $this->user->getOrganization();
        $org->expects($this->once())->method('hasAssociation')->willReturn(true);

        $orgs = new ArrayCollection();

        $org0 = $this->generateOrganizationEntity(
                    'testOrg0',
                    'testOrg0.name',
                    'org0.testCity',
                    'org0.testStreet',
                    'org0.1234'
        );

        $org1 = $this->generateOrganizationEntity('testOrg1', 'testOrg1.name', 'org1.city', 'org1.street', 'org1.number');
        $orgs->add($org1);

        $org->expects($this->once())->method('getHiringOrganizations')->willReturn($orgs);

        $org->expects($this->once())->method('getOrganization')->willReturn($org0);

        $expect = array(
            'testOrg0' => 'testOrg0.name|org0.testCity|org0.testStreet|org0.1234|',
            'testOrg1' => 'testOrg1.name|org1.city|org1.street|org1.number|'
        );

        $select = $this->target->__invoke($this->services,'irrelevant');

        $actual = $select->getValueOptions();

        $this->assertEquals($expect, $actual);
    }

    private function generateOrganizationEntity($id, $name, $city, $street, $number)
    {
        $org = new Organization();
        $name = new OrganizationName($name);
        $org->setOrganizationName($name);
        $org->setId($id);
        $orgContact = new OrganizationContact();
        $orgContact->setCity($city)
                    ->setStreet($street)
                    ->setHouseNumber($number);
        $org->setContact($orgContact);

        return $org;
    }
}
