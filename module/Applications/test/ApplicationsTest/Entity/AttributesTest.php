<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Applications\Entity\Attributes;

/**
 * Tests the Attributes entity.
 *
 * @covers \Applications\Entity\Attributes
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Applications
 * @group Applications.Entity
 */
class AttributesTest extends TestCase
{
    /**
     * @var \Applications\Entity\Attributes
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Attributes();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity
     */
    public function testExtendsAbstractEntity()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
    }

    /**
     * @dataProvider providerBooleanValues
     * @testdox test the privacy policy flag
     * @covers \Applications\Entity\Attributes::getAcceptedPrivacyPolicy
     * @covers \Applications\Entity\Attributes::setAcceptedPrivacyPolicy
     */
    public function testSetGetPrivacyPolicy($input, $expected)
    {
        $this->target->setAcceptedPrivacyPolicy($input);
        $this->assertEquals($expected, $this->target->getAcceptedPrivacyPolicy());
    }

    /**
     * @dataProvider providerBooleanValues
     * @testdox test the send carbon copy flag
     * @covers \Applications\Entity\Attributes::getSendCarbonCopy
     * @covers \Applications\Entity\Attributes::setSendCarbonCopy
     */
    public function testSetGetSendCarbonCopy($input, $expected)
    {
        $this->target->setSendCarbonCopy($input);
        $this->assertEquals($expected, $this->target->getSendCarbonCopy());
    }

    public function providerBooleanValues()
    {
        return [
            [1, true],
            [0, false],
            [true, true],
            [false, false],
            ["true", true],
            ["false", true],
            [null, false]
        ];
    }
}
