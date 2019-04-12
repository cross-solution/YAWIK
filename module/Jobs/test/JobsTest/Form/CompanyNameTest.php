<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\Job;
use Jobs\Form\CompanyName;
use Organizations\Entity\Organization;

/**
 * Tests for \Jobs\Form\CompanyName
 *
 * @covers \Jobs\Form\CompanyName
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class CompanyNameTest extends TestCase
{

    /**
     * @testdox Extends \Core\Form\SummaryForm and implements \Zend\InputFilter\InputFilterProviderInterface
     */
    public function testExtendsSummaryFormAndImplementsInputFilterProviderInterface()
    {
        $target = new CompanyName();

        $this->assertInstanceOf('\Core\Form\SummaryForm', $target, 'Wrong parent class!');
        $this->assertInstanceOf('\Zend\InputFilter\InputFilterProviderInterface', $target, 'Missing interface implementation.');
    }

    /**
     * @testdox Defines default values for properties derived from \Core\Form\SummaryForm
     */
    public function testDefaultPropertiesValues()
    {
        $target = new CompanyName();

        $this->assertAttributeEquals('Jobs/CompanyNameFieldset', 'baseFieldset', $target, 'baseFieldset set incorrect.');
        $this->assertAttributeEquals('Companyname', 'label', $target, 'label set incorrect.');
    }

    /**
     * @testdox returns a \Core\Entity\Hydrator\EntityHydrator if none is set.
     */
    public function testReturnsEntityHydrator()
    {
        $target = new CompanyName();
        $baseFieldsetMock = $this->getMockBuilder('\Jobs\Form\CompanyNameFieldset')->disableOriginalConstructor()->getMock();
        $baseFieldsetMock->expects($this->once())->method('setHydrator')->will($this->returnSelf());

        $target->setBaseFieldset($baseFieldsetMock);


        $hydrator = $target->getHydrator();


        $this->assertInstanceOf('\Core\Entity\Hydrator\EntityHydrator', $hydrator);
    }

    public function testReturnsInputFilterSpecificationsForItsElements()
    {
        $formName = 'TestCompanyName';
        $target = new CompanyName();
        $baseFieldsetMock = $this->getMockBuilder('\Jobs\Form\CompanyNameFieldset')->disableOriginalConstructor()->getMock();
        $baseFieldsetMock->expects($this->once())->method('getAttribute')->willReturn($formName);


        $target->setBaseFieldset($baseFieldsetMock);


        $expected = array(
            $formName => array(
                'type' => 'Jobs/Company'
            )
        );

        $this->assertEquals($expected, $target->getInputFilterSpecification());
    }

    public function provideInjectOrganizationTestData()
    {
        $org = new Organization();
        $org->setId('alreadyHereOrg');

        $job = new Job();
        $job->setOrganization($org);

        return array(
            array(new \stdClass, 'default'),
            array($job, 'default'),
            array(new Job(), 'one'),
            array(new Job(), 'more'),
        );
    }

    /**
     * @testdox injects organization into the job entity if no hiring organizations are set.
     * @dataProvider provideInjectOrganizationTestData
     */
    public function testSetObjectInjectsOrganizationsInJobEntity($object, $setupKey)
    {
        $mocks = $this->setupInjectOrganizationTestMocks($setupKey, $object);

        $actual = $mocks->target->setObject($object);

        $this->assertSame($actual, $mocks->target, 'Fluent interface broken');
        if ('one' == $setupKey) {
            $this->assertEquals(CompanyName::DISPLAY_SUMMARY, $mocks->target->getDisplayMode());
        }
    }

    private function setupInjectOrganizationTestMocks($key, $object)
    {
        $fs = $this->getMockBuilder('\Jobs\Form\CompanyNameFieldset')->disableOriginalConstructor()->getMock();
        $select = $this->getMockBuilder('\Jobs\Form\HiringOrganizationSelect')->disableOriginalConstructor()->getMock();
        $hydrator = $this->getMockBuilder('\Core\Entity\Hydrator\EntityHydrator')->disableOriginalConstructor()->getMock();

        switch ($key) {
            default:
                $fs->expects($this->never())->method('get');
                $fs->expects($this->never())->method('getHydrator');
                $hydrator->expects($this->never())->method('hydrate');
                break;

            case 'one':
                $orgValues = array('id1' => 'testOnlyOne');
                $select->expects($this->once())->method('setValue')->with(key($orgValues));
                $fs->expects($this->once())->method('getHydrator')->willReturn($hydrator);
                $hydrator->expects($this->once())->method('hydrate')->with(array('companyId' => key($orgValues)), $object);

                // fall through intentinally
                // no break
            case 'more':
                $fs->expects($this->once())->method('get')->with('companyId')->willReturn($select);
                if (!isset($orgValues)) {
                    $orgValues = array('id1' => 'test1', 'id2', 'test2');
                }
                $select->expects($this->once())->method('getValueOptions')->willReturn($orgValues);
                break;
        }

        $mocks = new \stdClass();
        $mocks->target = new CompanyName();
        $mocks->target->setBaseFieldset($fs);
        $mocks->baseFieldset = $fs;
        $mocks->selectElement = $select;
        $mocks->hydrator = $hydrator;

        return $mocks;
    }
}
