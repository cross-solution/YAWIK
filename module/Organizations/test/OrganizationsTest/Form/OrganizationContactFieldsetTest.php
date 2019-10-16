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
use Organizations\Form\OrganizationsContactFieldset;
use Organizations\Entity\OrganizationContact;

/**
 * Test for the organization contact form.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @covers \Organizations\Form\OrganizationsContactFieldset
 * @group Organizations
 * @group Organizations.Form
 */
class OrganizationContactFieldsetTest extends TestCase
{

    /*
     * @var $target OrganizationsContactFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationsContactFieldset();
        $this->target->init();
    }
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Organizations\Form\OrganizationsContactFieldset', $this->target);
        $this->assertInstanceOf('Zend\Form\Fieldset', $this->target);
    }

    public function testNameFormFields()
    {
        $this->assertSame($this->target->getName(), "contact");
        $this->assertTrue($this->target->has('fax'));
        $this->assertTrue($this->target->has('phone'));
        $this->assertTrue($this->target->has('city'));
        $this->assertTrue($this->target->has('postalcode'));
        $this->assertTrue($this->target->has('houseNumber'));
        $this->assertTrue($this->target->has('street'));
    }

    public function testAllowObjectBinding()
    {
        $this->assertSame($this->target->allowObjectBinding(new OrganizationContact), true);
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
