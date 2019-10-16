<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Form\OrganizationsDescriptionFieldset;

/**
 * Test for the organization description form.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @covers \Organizations\Form\OrganizationsDescriptionFieldset
 * @group Organizations
 * @group Organizations.Form
 */
class OrganizationDescriptionFieldsetTest extends TestCase
{

    /*
     * @var $target OrganizationsDescriptionFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationsDescriptionFieldset();
        $this->target->init();
    }
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Organizations\Form\OrganizationsDescriptionFieldset', $this->target);
        $this->assertInstanceOf('Zend\Form\Fieldset', $this->target);
    }

    public function testNameFormFields()
    {
        $this->assertSame($this->target->getName(), "organization-description");
        $this->assertTrue($this->target->has('description'));
    }

    public function testGetInputFilterSpec()
    {
        $this->assertSame($this->target->getInputFilterSpecification(), []);
    }

    public function testGetHydrator()
    {
        $this->assertEquals($this->target->getHydrator(), new EntityHydrator());
    }
}
