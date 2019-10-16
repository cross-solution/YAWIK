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

use Core\Form\DefaultButtonsFieldset;

/**
* @covers \Core\Form\DefaultButtonsFieldset
*/
class DefaultButtonsFieldsetTest extends TestCase
{
    /**
     * @var  $target DefaultButtonsFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new DefaultButtonsFieldset();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\DefaultButtonsFieldset', $this->target);
        $this->assertInstanceOf('Core\Form\ButtonsFieldset', $this->target);
    }

    /**
    * @todo
    */
    /*
    public function testInit()
    {
    }
    */
}
