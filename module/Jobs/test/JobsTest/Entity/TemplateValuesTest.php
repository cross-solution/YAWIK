<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Entity;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\TemplateValues;

/**
 * Test the template values of a job entity.
 *
 * @covers \Jobs\Entity\TemplateValues
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Jobs
 * @group Jobs.Entity
 */
class TemplateValuesTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var TemplateValues
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new TemplateValues();
    }
    /**
     * Does the entity implement the correct interface?
     */
    public function testTemplateImplementsInterface()
    {
        $this->assertInstanceOf('\Jobs\Entity\TemplateValuesInterface', $this->target);
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
            array('setRequirements', 'getRequirements', 'test1'),
            array('setBenefits', 'getBenefits', 'test2'),
            array('setQualifications', 'getQualifications', 'test2'),
            array('setDescription', 'getDescription', 'test2'),
            array('setTitle', 'getTitle', 'test2'),
            array('setLanguage', 'getLanguage', 'test2'),
            array(null , 'getRequirements', ''),
            array(null , 'getBenefits', ''),
            array(null , 'getQualifications', ''),
            array(null , 'getTitle', ''),
            array(null , 'getLanguage', 'en'),
        );
    }
}
