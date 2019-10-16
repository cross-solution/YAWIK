<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace ApplicationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Applications\Entity\Settings;
use Applications\Entity\SettingsInterface;
use Settings\Entity\ModuleSettingsContainer;

/**
 * Tests for Settings
 *
 * @covers \Applications\Entity\Settings
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class SettingsTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Settings
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Settings();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\Settings
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Settings\Entity\ModuleSettingsContainer', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Settings', $this->target);
        $this->assertInstanceOf('\Applications\Entity\SettingsInterface', $this->target);
    }

    /**
     * @dataProvider providerBoolean
     * @covers \Applications\Entity\Settings::setMailAccess
     * @covers \Applications\Entity\Settings::getMailAccess
     */
    public function testSetGetMailAccess($input, $expected)
    {
        $this->target->setMailAccess($input);
        $this->assertEquals($this->target->getMailAccess(), $expected);
    }
    

    public function providerBoolean()
    {
        return [
            [1, true],
            [true, true],
            [false,false],
            ["1", true],
        ];
    }
}
