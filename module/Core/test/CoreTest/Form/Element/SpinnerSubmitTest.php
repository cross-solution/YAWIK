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

use Core\Form\Element\SpinnerSubmit;

/**
 * @covers \Core\Form\Element\SpinnerSubmit
 */
class SpinnerSubmitTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var SpinnerSubmit
     */
    protected $target;

    public function setUp(){
        $this->target = new SpinnerSubmit();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\SpinnerSubmit', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
        $this->assertAttributeSame('spinnerButton','viewHelper',$this->target);
    }

    public function testSetGetViewHelper()
    {
        $expected = "my/view/helper";
        $this->target->setViewHelper($expected);
        $this->assertEquals($expected, $this->target->getViewHelper());
    }
}
