<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationsTest\Form;

use Applications\Form\Apply;

/**
* @covers \Applications\Form\Apply
*/
class ApplyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $target Apply
     */
    protected $target;

    public function setUp()
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
