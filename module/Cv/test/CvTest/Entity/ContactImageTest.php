<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use Core\Entity\FileInterface;
use PHPUnit\Framework\TestCase;

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

    private $inheritance = [ FileInterface::class ];

    public function propertiesProvider()
    {
        return [
            [ 'uri', [
                'pre' => function () {
                    $this->target->setId('some-id')->setName('some-name');
                },
                'ignore_setter' => true,
                'value' => '/file/Cv.ContactImage/some-id/' . urlencode('some-name'),
            ]],
        ];
    }
}
