<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\FileEntity;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Contact;
use Cv\Entity\ContactImage;

/**
 * Class ContactImageTest
 * @covers \Cv\Entity\ContactImage
 * @package CvTest\Entity
 */
class ContactImageTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        ContactImage::class,
    ];

    private $inheritance = [ FileEntity::class ];

    public function propertiesProvider()
    {
        return [
            [ 'contact', '@' . Contact::class ],
            [ 'uri', [
                'pre' => function () {
                    $this->target->setId('some-id')->setName('some-name');
                },
                'ignore_setter' => true,
                'value' => '/file/Cv.ContactImage/some-id/' . urlencode('some-name'),
            ]],
        ];
    }

    public function testShouldSetImageToNullOnRemove()
    {
        $mock = $this->getMockBuilder(Contact::class)
            ->getMock();
        $mock
            ->expects($this->once())
            ->method('setImage')
            ->with(null);

        $this->target->setContact($mock);
        $this->target->preRemove();
    }
}
