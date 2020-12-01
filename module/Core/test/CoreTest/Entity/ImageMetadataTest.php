<?php

declare(strict_types=1);

namespace CoreTest\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\EntityTrait;
use Core\Entity\FileMetadataInterface;
use Core\Entity\FileMetadataTrait;
use Core\Entity\Image;
use Core\Entity\ImageMetadata;
use Core\Entity\PermissionsInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageMetadataTest
 *
 * @covers \Core\Entity\ImageMetadata
 * @package CoreTest\Entity
 */
class ImageMetadataTest extends TestCase
{
    use TestInheritanceTrait,
        TestUsesTraitsTrait,
        TestSetterGetterTrait;

    protected $target = ImageMetadata::class;

    protected $inheritance = [
        FileMetadataInterface::class
    ];

    protected $traits = [
        EntityTrait::class,
        FileMetadataTrait::class
    ];

    public function propertiesProvider()
    {
        $user = $this->createMock(UserInterface::class);
        $permissions = $this->createMock(PermissionsInterface::class);
        return [
            ['belongsTo',[
                'default' => null,
                'setter_args' => ['test'],
                'expect' => 'test'
            ]],
            ['key', [
                'default' => null,
                'setter_args' => ['test'],
                'expected' => 'bar',
            ]],
            ['contentType',[
                    'default' => null,
                    'setter_args' => 'image/jpeg',
                    'expected' => 'image/jpeg',
            ]],
            ['user',[
                    'default' => null,
                    'setter_args' => $user,
                    'expected' => $user
            ]],
            ['permissions',[
                'default' => null,
                'setter_args' => $permissions,
                'expected' => $permissions
            ]],
        ];
    }

    public function testShouldBeAnImageMetadata()
    {
        $ob = new ImageMetadata();
        parent::assertEquals(Image::class, $ob->getOwnerFileClass());
    }
}
