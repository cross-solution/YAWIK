<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use Organizations\Entity\OrganizationName;


/**
 * Test the template entity.
 *
 * @covers Organizations\Entity\OrganizationName
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationNameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Class under Test
     *
     * @var OrganizationName
     */
    private $target;

    public function setup()
    {
        $this->target = new OrganizationName();
    }
    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\OrganizationName', $this->target);
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
            array('setId', 'getId', 'test1'),
            array('setName', 'getName', 'test1'),
            array('setRankingByCompany', 'getRankingByCompany', 'test1'),
        );
    }
}