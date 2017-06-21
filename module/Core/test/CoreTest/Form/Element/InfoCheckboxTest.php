<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form\Element;

use Core\Form\Element\InfoCheckbox;

/**
* @covers \Core\Form\Element\InfoCheckbox
*/
class InfoCheckboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InfoCheckbox
     */
    protected $target;

    public function setUp(){
        $this->target = new InfoCheckbox();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\InfoCheckbox', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
        $this->assertAttributeSame('formInfoCheckBox',  'helper', $this->target);
    }
}