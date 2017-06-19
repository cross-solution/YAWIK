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

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Factory\Form\ActiveOrganizationSelectFactory;
use Jobs\Form\OrganizationSelect;
use Organizations\Repository\Organization;
use Organizations\Entity\OrganizationContact;
use Organizations\Entity\OrganizationName;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Jobs\Factory\Form\ActiveOrganizationSelect
 * 
 * @covers \Jobs\Factory\Form\ActiveOrganizationSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Form
 */
class ActiveOrganizationSelectFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|ActiveOrganizationSelectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ActiveOrganizationSelectFactory::class,
        '@testCreateService' => ['mock' => ['__invoke' => ['count' => 1]]],
    ];

    private $inheritance = [ FactoryInterface::class ];


    public function testCreateService()
    {
        $container = $this->getServiceManagerMock();
        $formElements = $this->getPluginManagerMock($container);

        $this->target->__invoke($container,OrganizationSelect::class);
    }

    public function testServiceCreationWithoutPreSelectedOrganization()
    {
        $this->serviceCreation();
    }

    public function testServiceCreationWithPreSelectOrg()
    {
        $this->serviceCreation(true);
    }

    private function serviceCreation($withPreSelectOrg = false)
    {
        $services = [];
        $request = new Request();
        $query   = $request->getQuery();

        $services['Request'] = $request;

        if ($withPreSelectOrg) {
            $query->set('companyId', 'orgId');
            $org = new OrganizationEntityMock([
                'id' => 'orgId',
                'name' => 'TestOrg',
                'city' => 'TestCity',
                'street' => 'TestStreet',
                'number' => '123',
            ]);
            $repository = $this
	            ->getMockBuilder(Organization::class)
	            ->disableOriginalConstructor()
                ->setMethods(['find'])
                ->getMock()
            ;
            $repository
	            ->expects($this->once())
	            ->method('find')
	            ->with('orgId')
	            ->willReturn($org)
            ;
            $repositories = $this->createPluginManagerMock(['Organizations' => $repository]);
            $services['repositories'] = $repositories;
        }

        $container = $this->createServiceManagerMock($services);

        $select = $this->target->__invoke($container, 'irrelevant');

        if (!$withPreSelectOrg) {
            $this->assertInstanceOf(OrganizationSelect::class, $select);
            $this->assertEquals('?ajax=jobs.admin.activeorganizations', $select->getAttribute('data-ajax'));
        } else {
            $this->assertEquals(2, count($select->getValueOptions()));
        }

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

    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function getImage()
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }
}
