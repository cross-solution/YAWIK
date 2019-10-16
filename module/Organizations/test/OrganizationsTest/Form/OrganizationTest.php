<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form;

use PHPUnit\Framework\TestCase;

use Organizations\Form\Organizations;

/**
 * Test for the organization form.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @covers \Organizations\Form\Organizations
 * @group Organizations
 * @group Organizations.Form
 */
class OrganizationTest extends TestCase
{
    protected $target;

    protected function setUp(): void
    {
        $this->target = new Organizations();
        $this->target->init();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Organizations\Form\Organizations', $this->target);
    }

    public function testName()
    {
        $this->assertSame($this->target->getName(), 'organization-form');
    }

    public function testNumberOfField()
    {
        $this->assertSame($this->target->count(), 7);
    }
}
