<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use Core\Entity\FileEntity;
use Cv\Entity\Contact;
use Cv\Entity\ContactImage;

/**
 * Class ContactImageTest
 * @covers  Cv\Entity\ContactImage
 * @package CvTest\Entity
 */
class ContactImageTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldImplementsTheInfoInterface()
    {
        $this->assertInstanceOf(FileEntity::class, new ContactImage());
    }

    public function testShouldSetImageToNullOnRemove()
    {
        $mock = $this->getMockBuilder(Contact::class)
            ->getMock();
        $mock
            ->expects($this->once())
            ->method('setImage')
            ->with(null);
        $ob = new ContactImage();
        $ob->setContact($mock);

        $ob->preRemove();
    }

    public function testGetUriShouldConvertValueToCorrectUriPath()
    {
        $ob = new ContactImage();
        $ob
            ->setId('some-id')
            ->setName('some-name');

        $this->assertEquals(
            '/file/Cv.ContactImage/some-id/' . urlencode('some-name'),
            $ob->getUri()
        );
    }

    public function testSetAndGetContact()
    {
        $ob = new ContactImage();
        $contact = new Contact();

        $ob->setContact($contact);
        $this->assertSame($contact, $ob->getContact());
    }
}
