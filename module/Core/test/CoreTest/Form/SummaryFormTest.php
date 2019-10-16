<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\SummaryForm;
use Core\Form\SummaryFormInterface;

/**
* @covers \Core\Form\SummaryForm
*/
class SummaryTest extends TestCase
{
    /**
     * @var  $target SummaryForm
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new SummaryForm();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\SummaryForm', $this->target);
        $this->assertInstanceOf('Zend\Form\Form', $this->target);
    }

    /**
     * @dataProvider providerRenderMode
     * @covers \Core\Form\SummaryForm::setRenderMode
     * @covers \Core\Form\SummaryForm::getRenderMode
     *
     * @param string $input     render mode
     * @param string $expected  render mode
     */
    public function testSetGetRenderModer($input, $expected)
    {
        $this->target->setRenderMode($input);
        $this->assertSame($this->target->getRenderMode(), $expected);
    }

    public function providerRenderMode()
    {
        return [
            [SummaryFormInterface::RENDER_FORM,SummaryFormInterface::RENDER_FORM],
            [SummaryFormInterface::RENDER_SUMMARY,SummaryFormInterface::RENDER_SUMMARY],
            [SummaryFormInterface::RENDER_ALL, SummaryFormInterface::RENDER_ALL],
        ];
    }

    /**
     * @dataProvider providerDisplayMode
     * @covers \Core\Form\SummaryForm::setDisplayMode
     * @covers \Core\Form\SummaryForm::getDisplayMode
     *
     * @param string $input     display mode
     * @param string $expected  display mode
     */
    public function testSetGetDisplayModer($input, $expected)
    {
        $this->target->setDisplayMode($input);
        $this->assertSame($this->target->getDisplayMode(), $expected);
    }

    public function providerDisplayMode()
    {
        return [
            [SummaryFormInterface::DISPLAY_FORM,SummaryFormInterface::DISPLAY_FORM],
            [SummaryFormInterface::DISPLAY_SUMMARY,SummaryFormInterface::DISPLAY_SUMMARY],
        ];
    }

    /**
    * @todo
    */
    /*
    public function testIsValid()
    {
    }
    */

    /**
     * @dataProvider providerDisplayMode
     * @covers \Core\Form\SummaryForm::setOptions
     *
     * @param string $input     display mode
     * @param string $expected  display mode
     */
    public function testSetOptions($input, $expected)
    {
        $this->target->setOptions(['display_mode' => $input ]);
        $this->assertSame($this->target->getDisplayMode(), $expected);
    }
}
