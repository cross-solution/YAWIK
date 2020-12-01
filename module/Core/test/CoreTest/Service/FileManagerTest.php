<?php

declare(strict_types=1);

namespace CoreTest\Service;

use Auth\AuthenticationService;
use Auth\Entity\UserInterface;
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

    private FileManager $fileManager;

    /**
     * @var AuthenticationService|MockObject
     */
    private $auth;

    protected function setUp()
    {
        $this->dm = $this->createMock(DocumentManager::class);
        $this->auth = $this->createMock(AuthenticationService::class);
        $this->fileManager = new FileManager($this->dm, $this->auth);
    }

    public function testUploadFromFile()
    {
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $auth = $this->auth;

        $repo = $this->createMock(GridFSRepository::class);
        $metadata = $this->createMock(FileMetadataInterface::class);
        $user = $this->createMock(UserInterface::class);
        $expected = new \stdClass();

        $repo->expects($this->once())
            ->method('uploadFromFile')
            ->with('source', 'filename', $this->isInstanceOf(UploadOptions::class))
            ->willReturn($expected);

        $dm->expects($this->once())
            ->method('getRepository')
            ->with('some_class')
            ->willReturn($repo);

        $auth->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $retVal = $fileManager->uploadFromFile('some_class', $metadata, 'source', 'filename');
        $this->assertSame($expected, $retVal);
    }
}
