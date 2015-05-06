<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use Jobs\Form\HiringOrganizationSelect;

/**
 * Tests for HiringOrganizationSelect
 *
 * @covers \Jobs\Form\HiringOrganizationSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class HiringOrganizationSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var HiringOrganizationSelect
     */
    private $target;

    public function setUp()
    {
        $this->target = new HiringOrganizationSelect();
    }

    /**
     * @testdox Extends \Zend\Form\Element\Select and Implements \Core\Form\ViewPartialProviderInterface
     */
    public function testExtendsZFSelectAndImplementsViewPartialProviderInterface()
    {
        $this->assertInstanceOf('\Zend\Form\Element\Select', $this->target);
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $this->target);
    }

    /**
     * @testdox Provides default view partial name
     */
    public function testDefaultViewPartialIsSet()
    {
        $expected = 'jobs/form/hiring-organization-select';
        $actual   = $this->target->getViewPartial();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox Allows the view partial name to be changed.
     */
    public function testSetAndGetViewPartial()
    {
        $expected = 'new/partial/name';

        $this->assertSame($this->target, $this->target->setViewPartial($expected), 'setViewPartial() breaks fluent interface.');
        $this->assertEquals($expected, $this->target->getViewPartial());
    }
}