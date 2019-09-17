<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Form\AtsMode;

/**
 * Tests for AtsMode
 *
 * @covers \Jobs\Form\AtsMode
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class AtsModeTest extends TestCase
{

    /**
     * @testdox Extends \Core\Form\SummaryForm
     */
    public function testExtensionAndDefaultAttributes()
    {
        $target = new AtsMode();

        $this->assertInstanceOf('\Core\Form\SummaryForm', $target);
        $this->assertAttributeEquals('Jobs/AtsModeFieldset', 'baseFieldset', $target);
        $this->assertAttributeEquals(AtsMode::DISPLAY_SUMMARY, 'displayMode', $target);
    }

    /**
     * @testdox Sets "headscript" option in initialization
     */
    public function testInit()
    {
        /* @var $target AtsMode */
        $target = $this->getMockBuilder('\Jobs\Form\AtsMode')
                       ->disableOriginalConstructor()
                       ->setMethods(array('addBaseFieldset', 'addButtonsFieldset'))
                       ->getMock();

        $target->init();

        $this->assertEquals('modules/Jobs/js/form.ats-mode.js', $target->getOption('headscript'));
    }
}
