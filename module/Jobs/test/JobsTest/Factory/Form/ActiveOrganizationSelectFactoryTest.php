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

use Jobs\Factory\Form\ActiveOrganizationSelectFactory;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationName;

/**
 * Tests for \Jobs\Factory\Form\ActiveOrganizationSelect
 * 
 * @covers \Jobs\Factory\Form\ActiveOrganizationSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class ActiveOrganizationSelectFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsFactoryInterface()
    {
        $target = new ActiveOrganizationSelectFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', $target);
    }

    public function testCreatesOrganizationSelect()
    {
        $org1Values = [
            'id' => 'org1',
            'name' => 'Org1',
            'city' => 'Org1City',
            'street' => 'Org1Street',
            'number' => '1'
        ];

        $org1 = new OrganizationEntityMock($org1Values);
        $orgs = [ $org1 ];


        $jobRepo = $this->getMockBuilder('\Jobs\Repository\Job')->disableOriginalConstructor()->getMock();

        $jobRepo->expects($this->once())->method('findActiveOrganizations')->willReturn($orgs);

        $repos = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();

        $repos->expects($this->once())->method('get')->with('Jobs')->willReturn($jobRepo);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $services->expects($this->once())->method('get')->with('repositories')->willReturn($repos);

        $formsMock = $this->getMockBuilder('\Zend\Form\FormElementManager')->disableOriginalConstructor()->getMock();

        $formsMock->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $target = new ActiveOrganizationSelectFactory();

        $select = $target->createService($formsMock);

        $this->assertInstanceOf('\Jobs\Form\OrganizationSelect', $select);

        $actual = $select->getValueOptions();
        $expected = [ '0' => '', 'org1' => 'Org1|Org1City|Org1Street|1|' ];

        $this->assertEquals($expected, $actual);
    }
}

class OrganizationEntityMock extends Organization
{
    public function __construct(array $values)
    {
        $this->id = $values['id'];
        $this->organizationName = new OrganizationName($values['name']);
        $contact = new OrganizationContact();
        $contact->setCity($values['city'])->setStreet($values['street'])->setHouseNumber($values['number']);
        $this->contact = $contact;
    }
}
