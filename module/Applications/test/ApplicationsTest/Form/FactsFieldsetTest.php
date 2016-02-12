<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationsTest\Form;

use Applications\Form\FactsFieldset;


/**
* @covers \Applications\Form\FactsFieldset
*/
class FactsFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $target FactsFieldset
     */
    protected $target;

    public function setUp(){
        $this->target = new FactsFieldset();
        $this->target->init();
    }

    public function testConstructor(){
        $this->assertInstanceOf('Applications\Form\FactsFieldset', $this->target);
    }
    public function testFormName()
    {
        $this->assertEquals($this->target->getName(),'base');
    }

    public function testSetGetEmptySummaryText(){
        $input="Klick here";
        $this->target->setEmptySummaryNotice($input);
        $this->assertSame($this->target->getEmptySummaryNotice(),$input);
    }

    /**
     * @dataProvider providerDisableCapable
     */
    public function testSetGetIsDisableCapable($input, $expected){
        $this->target->setIsDisableCapable($input);
        $this->assertSame($this->target->isDisableCapable(),$expected);
    }

    public function   providerDisableCapable() {
        return [
            [true,true],
            [false,false],
            [null, true]
        ];
    }

    /**
     * @dataProvider providerDisableElementCapable
     */
    public function testSetGetIsDisableElementCapable($input, $expected){
        $this->target->setIsDisableElementsCapable($input);
        $this->assertSame($this->target->isDisableElementsCapable(),$expected);
    }

    public function   providerDisableElementCapable() {
        return [
            [true,true],
            [false,false],
            [null, true]
        ];
    }

    public function testIsSummaryEmptyDefaultTrue() {
        $this->assertSame($this->target->isSummaryEmpty(),true);
    }

    public function testIsSummaryEmptyDefaultFalse() {
        $expectedSalary = $this->target->get('expectedSalary');
        $expectedSalary->setValue(10000);
        $this->assertSame($this->target->isSummaryEmpty(),false);
    }

    public function testDisableElements() {
        $expectedSalary = $this->target->get('expectedSalary');
        $expectedSalary->setValue(10000);
        $this->target->disableElements(['expectedSalary','willingnessToTravel']);
        $this->assertSame($this->target->isSummaryEmpty(),true);
    }


}