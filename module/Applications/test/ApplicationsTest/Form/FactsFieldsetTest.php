<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationsTest\Form;

use PHPUnit\Framework\TestCase;

use Applications\Form\FactsFieldset;
use Core\Form\Element\DatePicker;
use Zend\Form\Factory;

/**
* @covers \Applications\Form\FactsFieldset
*/
class FactsFieldsetTest extends TestCase
{
    /**
     * @var $target FactsFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new FactsFieldset();

        // Setup form factory.
        $factory = new Factory();
        $elements = $factory->getFormElementManager();
        $elements->setService('Core/Datepicker', new DatePicker());

        // inject form factory
        $this->target->setFormFactory($factory);
        $this->target->init();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Applications\Form\FactsFieldset', $this->target);
    }
    public function testFormName()
    {
        $this->assertEquals($this->target->getName(), 'base');
    }

    public function testSetGetEmptySummaryText()
    {
        $input="Klick here";
        $this->target->setEmptySummaryNotice($input);
        $this->assertSame($this->target->getEmptySummaryNotice(), $input);
    }

    /**
     * @dataProvider providerDisableCapable
     */
    public function testSetGetIsDisableCapable($input, $expected)
    {
        $this->target->setIsDisableCapable($input);
        $this->assertSame($this->target->isDisableCapable(), $expected);
    }

    public function providerDisableCapable()
    {
        return [
            [true,true],
            [false,false],
            [null, true]
        ];
    }

    /**
     * @dataProvider providerDisableElementCapable
     */
    public function testSetGetIsDisableElementCapable($input, $expected)
    {
        $this->target->setIsDisableElementsCapable($input);
        $this->assertSame($this->target->isDisableElementsCapable(), $expected);
    }

    public function providerDisableElementCapable()
    {
        return [
            [true,true],
            [false,false],
            [null, true]
        ];
    }

    public function testIsSummaryEmptyDefaultTrue()
    {
        $this->assertSame($this->target->isSummaryEmpty(), true);
    }

    public function testIsSummaryEmptyDefaultFalse()
    {
        $expectedSalary = $this->target->get('expectedSalary');
        $expectedSalary->setValue(10000);
        $this->assertSame($this->target->isSummaryEmpty(), false);
    }

    public function testDisableElements()
    {
        $expectedSalary = $this->target->get('expectedSalary');
        $expectedSalary->setValue(10000);
        $this->target->disableElements(['expectedSalary', 'willingnessToTravel']);
        $this->assertSame($this->target->isSummaryEmpty(), true);
    }
}
