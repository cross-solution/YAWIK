<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use Core\Form\DefaultButtonsFieldset;

/**
* @covers \Core\Form\DefaultButtonsFieldset
*/
class DefaultButtonsFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  $target DefaultButtonsFieldset
     */
    protected $target;

    public function setUp(){
        $this->target = new DefaultButtonsFieldset();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\DefaultButtonsFieldset', $this->target);
        $this->assertInstanceOf('Core\Form\ButtonsFieldset', $this->target);
    }


    public function testInit(){
        /*@todo*/
    }
}