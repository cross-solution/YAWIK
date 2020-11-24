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

use Core\Form\ListFilterButtonsFieldset;

/**
* @covers \Core\Form\ListFilterButtonsFieldset
*/
class ListFilterButtonsFieldsetTest extends TestCase
{
    /**
     * @var ListFilterButtonsFieldset
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ListFilterButtonsFieldset();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\ListFilterButtonsFieldset', $this->target);
        $this->assertInstanceOf('Core\Form\ButtonsFieldset', $this->target);
    }

    public function testInit()
    {
        $this->target->init();
        $this->assertSame($this->target->getViewPartial(), 'form/core/buttons');
        $this->assertSame($this->target->getName(), 'buttons');
        $this->assertSame($this->target->count(), 2);
        $this->assertSame($this->target->isDisableCapable(), false);
    }
}
