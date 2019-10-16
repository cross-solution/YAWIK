<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Entity;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\AtsMode;

/**
 * Tests for AtsMode
 *
 * @covers \Jobs\Entity\AtsMode
 * @coversDefaultClass \Jobs\Entity\AtsMode
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class AtsModeTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var AtsMode
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new AtsMode();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\AtsModeInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsAtsModeInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\AtsModeInterface', $this->target);
    }

    public function provideCreatingInstancesTestData()
    {
        return array(
            array(null, null, AtsMode::MODE_INTERN, null, null),
            array(AtsMode::MODE_NONE, null, AtsMode::MODE_NONE, null, null),
            array(AtsMode::MODE_NONE, 'something', AtsMode::MODE_NONE, null, null),
            array(AtsMode::MODE_INTERN, null, AtsMode::MODE_INTERN, null, null),
            array(AtsMode::MODE_INTERN, 'something', AtsMode::MODE_INTERN, null, null),
            array(AtsMode::MODE_URI, 'http://mode.uri.uri', AtsMode::MODE_URI, 'http://mode.uri.uri', null),
            array(AtsMode::MODE_URI, null, AtsMode::MODE_URI, null, null),
            array(AtsMode::MODE_EMAIL, 'an-email-address', AtsMode::MODE_EMAIL, null, 'an-email-address'),
            array(AtsMode::MODE_EMAIL, null, AtsMode::MODE_EMAIL, null, null),
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideCreatingInstancesTestData
     * @covers ::__construct
     *
     * @param string $actualMode       the mode to set
     * @param string $actualUriOrEmail the second argument for __construct()
     * @param string $expectedMode     the expected value for mode
     * @param string $expectedUri      the expected value for the URI
     * @param string $expectedEmail    the expected value for the email
     */
    public function testCreatingInstances($actualMode, $actualUriOrEmail, $expectedMode, $expectedUri, $expectedEmail)
    {
        $target = null === $actualMode ? new AtsMode() : new AtsMode($actualMode, $actualUriOrEmail);

        $this->assertAttributeEquals($expectedMode, 'mode', $target);
        $this->assertAttributeEquals($expectedUri, 'uri', $target);
        $this->assertAttributeEquals($expectedEmail, 'email', $target);
    }

    public function provideModeTestData()
    {
        return array(
            array(AtsMode::MODE_NONE),
            array(AtsMode::MODE_INTERN),
            array(AtsMode::MODE_URI),
            array(AtsMode::MODE_EMAIL),
        );
    }

    /**
     * @testdox      Allows setting and getting the mode
     * @dataProvider provideModeTestData
     *
     * @param string $mode
     */
    public function testSettingAndGettingMode($mode)
    {
        $this->target->setMode($mode);

        $this->assertEquals($mode, $this->target->getMode());
    }

    /**
     * @testdox setMode() throws an exception if invalid mode is passed.
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown value for ats mode.
     */
    public function testSetModeThrowsExceptionIfInvalidModeIsPassed()
    {
        $this->target->setMode('highly invalid mode name');
    }

    public function provideIsMethodsTestData()
    {
        return array(
            array(AtsMode::MODE_INTERN, true, false, true, false, false),
            array(AtsMode::MODE_URI, true, false, false, true, false),
            array(AtsMode::MODE_EMAIL, true, false, false, false, true),
            array(AtsMode::MODE_NONE, false, true, false, false, false),
        );
    }

    /**
     * @testdox      Allows checking for the mode via convinient isMODE methods
     * @dataProvider provideIsMethodsTestData
     *
     * @param string $mode
     * @param bool   $isEnabled  expected value for isEnabled()
     * @param bool   $isDisabled expected value for isDisabled()
     * @param bool   $isIntern   expected value for isIntern()
     * @param bool   $isUri      expected value for isUri()
     * @param bool   $isEmail    expected value for isEmail()
     */
    public function testIsMethods($mode, $isEnabled, $isDisabled, $isIntern, $isUri, $isEmail)
    {
        $this->target->setMode($mode);

        $this->assertEquals($isEnabled, $this->target->isEnabled(), 'isEnabled() returns wrong value.');
        $this->assertEquals($isDisabled, $this->target->isDisabled(), 'isDisabled() returns wrong value.');
        $this->assertEquals($isIntern, $this->target->isIntern(), 'isIntern() returns wrong value.');
        $this->assertEquals($isUri, $this->target->isUri(), 'isUri() returns wrong value.');
        $this->assertEquals($isEmail, $this->target->isEmail(), 'isEmail() returns wrong value.');
    }

    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingTheUri()
    {
        $uri = 'http://test.uri';

        $this->target->setUri($uri);

        $this->assertEquals($uri, $this->target->getUri());
    }

    /**
     * @testdox Allows setting and getting the email address
     */
    public function testSettingAndGettingTheEmail()
    {
        $email = 'test@mail';
        $this->target->setEmail($email);
        $this->assertEquals($email, $this->target->getEmail());
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testSetGetOneClickApply($input, $expected)
    {
        $this->target->setOneClickApply($input);
        $this->assertEquals($expected, $this->target->getOneClickApply());
    }

    public function provideBoolean()
    {
        return [
            [true,true],
            [false, false],
            [null, false]
        ];
    }

    /**
     * @dataProvider provideArrays
     */
    public function testSetGetOneClickApplyProfiles($input, $expected)
    {
        $this->target->setOneClickApplyProfiles($input);
        $this->assertEquals($expected, $this->target->getOneClickApplyProfiles());
    }

    public function provideArrays()
    {
        $array = ['facebook','linkedin','xing'];
        return [
            [$array,$array],
        ];
    }
}
