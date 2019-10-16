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

use Jobs\Form\HiringOrganizationSelect;

/**
 * Tests for HiringOrganizationSelect
 *
 * @covers \Jobs\Form\HiringOrganizationSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class HiringOrganizationSelectTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var HiringOrganizationSelect
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new HiringOrganizationSelect();
    }

    /**
     * @testdox Extends \Core\Form\Element\Select and Implements \Core\Form\ViewPartialProviderInterface
     */
    public function testExtendsZFSelectAndImplementsViewPartialProviderInterface()
    {
        $this->assertInstanceOf('\Core\Form\Element\Select', $this->target);
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
