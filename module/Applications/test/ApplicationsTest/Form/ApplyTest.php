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
use Jobs\Entity\Status;

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

    /**
     * @dataProvider providerFormActionsData
     */
    public function testFormActions($input, $expected)
    {
        $this->assertEquals($this->target->getActionFor($input), $expected);
    }

    public function providerFormActionsData()
    {
        return [
            ['facts','?form=facts'],
            ['profiles','?form=profiles'],
            ['attachments','?form=attachments'],
            ['attributes','?form=attributes'],
            ['contact','?form=contact'],
        ];
    }

    public function testFactsAction()
    {
        $this->target->count();
        $this->assertEquals($this->target->getOption("settings_label"), 'Customize apply form');
    }
}
