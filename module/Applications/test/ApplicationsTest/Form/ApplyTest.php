<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationsTest\Form;

use PHPUnit\Framework\TestCase;

use Applications\Form\Apply;

/**
* @covers \Applications\Form\Apply
*/
class ApplyTest extends TestCase
{
    /**
     * @var $target Apply
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new Apply();
        $this->target->init();
    }

    public function testFactsAction()
    {
        $this->target->count();
        $this->assertEquals($this->target->getOption("settings_label"), 'Customize apply form');
    }
}
