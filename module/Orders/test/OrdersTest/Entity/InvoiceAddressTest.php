<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace OrdersTest\Entity;

use Orders\Entity\InvoiceAddress;

/**
 * Tests for InvoiceAddress
 *
 * @covers \Orders\Entity\InvoiceAddress
 * @coversDefaultClass \Orders\Entity\InvoiceAddress
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Orders
 * @group  Orders.Entity
 */
class InvoiceAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var InvoiceAddress
     */
    private $target;

    public function setup()
    {
        $this->target = new InvoiceAddress();
    }

    /**
     * @coversNothing
     */
    public function testInstanceOfProduct()
    {
        $this->assertInstanceOf('\Orders\Entity\InvoiceAddressInterface', $this->target);
    }


    /**
     * @testdox Allows setting and getting the City
     */
    public function testSettingAndGettingCity()
    {
        $input = 'Frankfurt';

        $this->target->setCity($input);

        $this->assertEquals($input, $this->target->getCity());
    }

    /**
     * @testdox Allows setting and getting the Company name
     */
    public function testSettingAndGettingCompany()
    {
        $input = 'CROSS Solution';

        $this->target->setCompany($input);

        $this->assertEquals($input, $this->target->getCompany());
    }

    /**
     * @testdox Allows setting and getting the Country
     */
    public function testSettingAndGettingCountry()
    {
        $input = 'Deutschland';

        $this->target->setCountry($input);

        $this->assertEquals($input, $this->target->getCountry());
    }

    /**
     * @testdox Allows setting and getting the Email
     */
    public function testSettingAndGettingEmail()
    {
        $input = 'name@example.com';

        $this->target->setEmail($input);

        $this->assertEquals($input, $this->target->getEmail());
    }

    /**
     * @testdox Allows setting and getting the Name of a contact person
     */
    public function testSettingAndGettingName()
    {
        $input = 'Peter Musterann';

        $this->target->setName($input);

        $this->assertEquals($input, $this->target->getName());
    }

    /**
     * @testdox Allows setting and getting the region
     */
    public function testSettingAndGettingRegion()
    {
        $input = 'Hessen';

        $this->target->setRegion($input);

        $this->assertEquals($input, $this->target->getRegion());
    }

    /**
     * @testdox Allows setting and getting the street
     */
    public function testSettingAndGettingStreet()
    {
        $input = 'Diemelstrasse 2-4';

        $this->target->setStreet($input);

        $this->assertEquals($input, $this->target->getStreet());
    }

    /**
     * @testdox Allows setting and getting the gender of the contact person
     */
    public function testSettingAndGettingGender()
    {
        $input = 'm';

        $this->target->setGender($input);

        $this->assertEquals($input, $this->target->getGender());
    }

    /**
     * @testdox Allows setting and getting the VAT ID of the company
     */
    public function testSettingAndGettingVatIdNumber()
    {
        $input = '1234';

        $this->target->setVatIdNumber($input);

        $this->assertEquals($input, $this->target->getVatIdNumber());
    }

    /**
     * @testdox Allows setting and getting the postal code
     */
    public function testSettingAndGettingZipCode()
    {
        $input = '1234';

        $this->target->setZipCode($input);

        $this->assertEquals($input, $this->target->getZipCode());
    }

}
