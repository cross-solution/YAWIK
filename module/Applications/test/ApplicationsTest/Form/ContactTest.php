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

use Applications\Form\ContactContainer;

/**
* @covers \Applications\Form\ContactContainer
*/
class ContactTest extends TestCase
{
    /**
     * @var $target ContactContainer
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ContactContainer();
        $this->target->init();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Auth\Form\UserInfoContainer', $this->target);
        $this->assertInstanceOf('Applications\Form\ContactContainer', $this->target);
    }
}
