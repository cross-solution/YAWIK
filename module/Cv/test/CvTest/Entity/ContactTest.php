<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;


use Auth\Entity\Info;
use Auth\Entity\InfoInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Contact;

/**
 * Class ContactTest
 *
 * @covers \Cv\Entity\Contact
 * @group Cv
 * @group Cv.Entity
 */
class ContactTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|Contact
     */
    private $target = [
        Contact::class,
        '@testConstructorCallsInheritWhenUserInfoIsPassed' => false,
    ];

    private $inheritance = [ Info::class, InfoInterface::class ];


    public function testConstructorCallsInheritWhenUserInfoIsPassed()
    {
        $userInfo = new Info();

        $target = new ContactMock($userInfo);

        $this->assertTrue($target->inheritCalled);
        $this->assertSame($target->inheritCalledWith, $userInfo);

        $target = new ContactMock();

        $this->assertFalse($target->inheritCalled);
    }

    /**
     * Test Contact entity should inherit from InfoInterface
     */
    public function testShouldCopyFromInfoInterface()
    {
        $info = new Info();
        $info
            ->setFirstName('First Name')
            ->setLastName('Last Name')
            ->setBirthDay('01-01-1980');

        $ob = $this->target;
        $ob->inherit($info);

        $this->assertEquals($info->getFirstName(), $ob->getFirstName());
        $this->assertEquals($info->getLastName(), $ob->getLastName());
        $this->assertEquals($info->getBirthDay(), $ob->getBirthDay());
    }
}

class ContactMock extends Contact
{
    public $inheritCalled = false;
    public $inheritCalledWith;

    public function inherit(InfoInterface $info)
    {
        $this->inheritCalled = true;
        $this->inheritCalledWith = $info;
    }
}
