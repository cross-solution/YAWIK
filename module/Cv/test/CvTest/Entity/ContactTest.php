<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;


use Auth\Entity\Info;
use Auth\Entity\InfoInterface;
use Cv\Entity\Contact;

/**
 * Class ContactTest
 *
 * @covers  Cv\Entity\Contact
 * @package CvTest\Entity
 */
class ContactTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldImplementsTheInfoInterface()
    {
        $this->assertInstanceOf(InfoInterface::class, new Contact());
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

        $ob = new Contact($info);

        $this->assertEquals($info->getFirstName(), $ob->getFirstName());
        $this->assertEquals($info->getLastName(), $ob->getLastName());
        $this->assertEquals($info->getBirthDay(), $ob->getBirthDay());
    }
}
