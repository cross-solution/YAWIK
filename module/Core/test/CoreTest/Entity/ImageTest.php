<?php

declare(strict_types=1);

namespace CoreTest\Entity;

use Core\Entity\FileInterface;
use Core\Entity\FileTrait;
use Core\Entity\Image;
use Core\Entity\ImageInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use PHPUnit\Framework\TestCase;

class TestImage extends Image
{
    public function __construct()
    {
        $this->id = 'id';
        $this->name = 'name';
    }
}

/**
 * Class ImageTest
 *
 * @covers \Core\Entity\Image
 * @package CoreTest\Entity
 */
class ImageTest extends TestCase
{
    use TestInheritanceTrait,
        TestSetterGetterTrait,
        TestUsesTraitsTrait;

    protected $target = Image::class;

    protected $inheritance = [
        FileInterface::class,
        ImageInterface::class
    ];

    protected $traits = [
        FileTrait::class
    ];

    public function propertiesProvider()
    {
        $uploadDate = new \DateTime();
        return [
            ['id', [
                'default' => null,
                'setter_args' => 'some_id',
                'expected' => 'some_id'
            ]],
            ['name',[
                'default' => null,
            ]],
            ['uploadDate', [
                'default' => null,
            ]],
            ['length', [
                'default' => null,
            ]],
            ['chunkSize', [
                'default' => null
            ]],
            ['metadata', [
                'default' => null
            ]]
        ];
    }

    public function testGetUri()
    {
        $target = new TestImage();
        $expected = '/file/Core.Images/id/name';
        parent::assertSame($expected, $target->getUri());
    }
}
