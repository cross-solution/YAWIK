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

use Core\Form\Element\Checkbox;

/**
 * @covers \Core\Form\Element\Checkbox
 */
class CheckboxTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Checkbox
     */
    protected $target;

    public function setUp(){
        $this->target = new Checkbox();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\Checkbox', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
        $this->assertAttributeSame('formCheckBox','helper',$this->target);
    }

    public function testSetGetViewHelper()
    {
        $expected = "my/view/helper";
        $this->target->setViewHelper($expected);
        $this->assertEquals($expected, $this->target->getViewHelper());
    }
}
