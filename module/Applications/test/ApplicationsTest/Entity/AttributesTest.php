<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Entity;

use Applications\Entity\Attributes;

/**
 * Tests the Attributes entity.
 *
 * @covers \Applications\Entity\Attributes
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Applications
 * @group Applications.Entity
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Applications\Entity\Attributes
     */
    private $target;

    public function setUp()
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
     * @testdox test the privacy policy flag
     * @covers Applications\Entity\Attributes::getAcceptedPrivacyPolicy
     * @covers Applications\Entity\Attributes::setAcceptedPrivacyPolicy
     */
    public function testSetGetPrivacyPolicy()
    {
        $input = true;
        $this->target->setAcceptedPrivacyPolicy($input);
        $this->assertEquals($input, $this->target->getAcceptedPrivacyPolicy());
        $input = false;
        $this->target->setAcceptedPrivacyPolicy($input);
        $this->assertEquals($input, $this->target->getAcceptedPrivacyPolicy());
    }

    /**
     * @testdox test the send carbon copy flag
     * @covers Applications\Entity\Attributes::getSendCarbonCopy
     * @covers Applications\Entity\Attributes::setSendCarbonCopy
     */
    public function testSetGetSendCarbonCopy()
    {
        $input = true;
        $this->target->setSendCarbonCopy($input);
        $this->assertEquals($input, $this->target->getSendCarbonCopy());
        $input = false;
        $this->target->setSendCarbonCopy($input);
        $this->assertEquals($input, $this->target->getSendCarbonCopy());
    }
}