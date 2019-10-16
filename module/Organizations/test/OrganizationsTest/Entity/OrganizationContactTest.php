<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Organizations\Entity\OrganizationContact;

/**
 * Test the template entity.
 *
 * @covers \Organizations\Entity\OrganizationContact
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationContactTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var OrganizationContact
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new OrganizationContact();
    }
    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\OrganizationContact', $this->target);
    }


    /**
     * Do setter and getter methods work correctly?
     *
     * @param string $setter Setter method name
     * @param string $getter getter method name
     * @param mixed $value Value to set and test the getter method with.
     *
     * @dataProvider provideSetterTestValues
     */
    public function testSettingValuesViaSetterMethods($setter, $getter, $value)
    {
        $target = $this->target;

        if (is_array($value)) {
            $setValue = $value[0];
            $getValue = $value[1];
        } else {
            $setValue = $getValue = $value;
        }

        if (null !== $setter) {
            $object = $target->$setter($setValue);
            $this->assertSame($target, $object, 'Fluent interface broken!');
        }

        $this->assertSame($target->$getter(), $getValue);
    }


    /**
     * Provides datasets for testSettingValuesViaSetterMethods.
     *
     * @return array
     */
    public function provideSetterTestValues()
    {
        return array(
            ['setFax', 'getFax', 'test1'],
            ['setPhone', 'getPhone', 'test2'],
            ['setHouseNumber', 'getHouseNumber', 'test2'],
            ['setStreet', 'getStreet', 'test2'],
            ['setPostalcode', 'getPostalcode', 'test2'],
            ['setCity', 'getCity', 'test2'],
            ['setCountry', 'getCountry', 'Deutschland'],
            ['setWebsite', 'getWebsite', '0815.4711'],
        );
    }
}
