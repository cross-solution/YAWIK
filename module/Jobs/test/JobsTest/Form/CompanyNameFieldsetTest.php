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

use Jobs\Form\CompanyNameFieldset;

/**
 * Test for CompanyNameFieldset
 *
 * @covers \Jobs\Form\CompanyNameFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class CompanyNameFieldsetTest extends TestCase
{
    /**
     * Class under Test
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CompanyNameFieldset
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = $this->getMockBuilder('\Jobs\Form\CompanyNameFieldset')
                             ->disableOriginalConstructor()
                             ->setMethods(array('setAttribute', 'setName', 'add'))
                             ->getMock();
    }

    /**
     * @testdox Extends \Zend\Form\Fieldset
     */
    public function testExtendsFieldset()
    {
        $this->assertInstanceOf('\Zend\Form\Fieldset', $this->target);
    }

    /**
     * @testdox Configures itself in the init() method.
     */
    public function testInitialization()
    {
        $this->target->expects($this->once())
                     ->method('setAttribute')
                     ->with('id', 'jobcompanyname-fieldset');

        $this->target->expects($this->once())
                     ->method('setName')
                     ->with('jobCompanyName');

        $addParam1 = array(
            'type' => 'Jobs/HiringOrganizationSelect',
            'property' => true,
            'name' => 'companyId',
            'options' => array(
                'label' =>  'Companyname',
            ),
            'attributes' => array(
                'data-placeholder' => 'Select hiring organization',
                'data-allowclear' => 'false',
                'data-width' => '100%'
            ),
        );

        $addParam2 = [
            'type' => 'Jobs/ManagerSelect',
                'property' => true,
                'name' => 'managers',
                'options' => [
                    'description' => 'There are department managers assigned to your organization. Please select the department manager, who will receive notifications for incoming applications',
                    'label' => 'Choose Managers',
                ],
                'attributes' => [
                    'data-allowclear'  => true,
                    'data-width' => '100%',
                    'multiple' => true,
                    'class' => 'manager-select',
                    'data-organization-element' => 'organization-select',
                ],

            ];

        $this->target->expects($this->exactly(2))
                     ->method('add')
                     ->withConsecutive(
                         [$addParam1],
                         [$addParam2]
                    );

        $this->target->init();
    }
}
