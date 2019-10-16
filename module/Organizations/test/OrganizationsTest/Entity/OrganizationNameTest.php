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

use Organizations\Entity\OrganizationName;

/**
 * Test the template entity.
 *
 * @covers \Organizations\Entity\OrganizationName
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class OrganizationNameTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var OrganizationName
     */
    private $target;

    protected function setUp(): void
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

    public function testRefCounterDec()
    {
        $target = $this->target;
        $target->setRankingByCompany(10);
        $this->assertSame($target->refCompanyCounterDec()->getRankingByCompany(), 9);
    }

    public function testRefCounterInc()
    {
        $target = $this->target;
        $target->setRankingByCompany(10);
        $this->assertSame($target->refCompanyCounterInc()->getRankingByCompany(), 11);
    }

    public function testRefCounterIncrement()
    {
        $target = $this->target;
        $target->refCounterInc();
        $this->assertAttributeSame(1, 'ranking', $target);
    }

    public function testRefCounterDecrement()
    {
        $target = $this->target;
        $target->refCounterDec();
        $this->assertAttributeSame(-1, 'ranking', $target);
    }
}
