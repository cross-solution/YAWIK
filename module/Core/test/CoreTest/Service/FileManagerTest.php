<?php

declare(strict_types=1);

namespace CoreTest\Service;

use Core\Entity\FileMetadataInterface;
use Core\Service\FileManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Doctrine\ODM\MongoDB\Repository\UploadOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FileManagerTest
 *
 * @covers \Core\Service\FileManager
 * @package CoreTest\Manager
 */
class FileManagerTest extends TestCase
{
    /**
     * @var MockObject|DocumentManager
     */
    private $dm;

    /**
     * @var \Core\Service\FileManager
     */
    private \Core\Service\FileManager $fileManager;

    protected function setUp()
    {
        $this->dm = $this->createMock(DocumentManager::class);
        $this->fileManager = new \Core\Service\FileManager($this->dm);
    }

    public function testUploadFromFile()
    {
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $repo = $this->createMock(GridFSRepository::class);
        $metadata = $this->createMock(FileMetadataInterface::class);
        $expected = new \stdClass();

        $metadata->expects($this->once())
            ->method('getOwnerFileClass')
            ->willReturn('some_class');

        $repo->expects($this->once())
            ->method('uploadFromFile')
            ->with('source', 'filename', $this->isInstanceOf(UploadOptions::class))
            ->willReturn($expected);

        $dm->expects($this->once())
            ->method('getRepository')
            ->with('some_class')
            ->willReturn($repo);

        $retVal = $fileManager->uploadFromFile($metadata, 'source', 'filename');
        $this->assertSame($expected, $retVal);
    }
}
