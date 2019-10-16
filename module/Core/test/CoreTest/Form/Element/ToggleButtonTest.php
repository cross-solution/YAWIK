<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace CoreTest\Form\Element;

use PHPUnit\Framework\TestCase;

use Core\Form\Element\ToggleButton;

/**
 *
 * @backupGlobals disabled
 * @covers \Core\Form\Element\ToggleButton
 */
class ToggleButtonTest extends TestCase
{

    /**
     * @var ToggleButton
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ToggleButton();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\ToggleButton', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
        $this->assertAttributeSame('toggleButton', 'viewHelper', $this->target);
    }

    public function testSetGetViewHelper()
    {
        $expected = "my/view/helper";
        $this->target->setViewHelper($expected);
        $this->assertEquals($expected, $this->target->getViewHelper());
    }
}
