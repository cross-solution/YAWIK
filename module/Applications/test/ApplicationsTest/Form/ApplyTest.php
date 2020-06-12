<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
