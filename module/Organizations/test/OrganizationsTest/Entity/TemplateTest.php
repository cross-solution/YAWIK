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

use Organizations\Entity\Template;

/**
 * Test the template entity.
 *
 * @covers \Organizations\Entity\Template
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class TemplateTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var Template
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Template();
    }
    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\TemplateInterface', $this->target);
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
            array('setLabelRequirements', 'getLabelRequirements', 'test1'),
            array('setLabelBenefits', 'getLabelBenefits', 'test2'),
            array('setLabelQualifications', 'getLabelQualifications', 'test2'),
            array(null , 'getLabelRequirements', 'Requirements'),
            array(null , 'getLabelBenefits', 'Benefits'),
            array(null , 'getLabelQualifications', 'Qualifications'),

        );
    }
}
