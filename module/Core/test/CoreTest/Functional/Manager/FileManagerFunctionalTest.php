<?php

declare(strict_types=1);

namespace CoreTest\Functional\Manager;


use Core\Entity\Image;
use Core\Entity\ImageMetadata;
use Core\Service\FileManager;
use CoreTestUtils\TestCase\FunctionalTestCase;

/**
 * Class FileManager Functional Tests
 * @covers \Core\Service\FileManager
 * @covers \Core\Entity\ImageMetadata
 * @covers \Core\Entity\Image
 * @package CoreTest\Functional\Manager
 */
class FileManagerFunctionalTest extends FunctionalTestCase
{
    public function testAddImage()
    {
        $logo = __FILE__;
        $manager = $this->getService(FileManager::class);
        $metadata = new ImageMetadata();

        $file = $manager->uploadFromFile(Image::class, $metadata, $logo, 'logo.jpg');
        $this->assertInstanceOf(Image::class, $file);
    }
}