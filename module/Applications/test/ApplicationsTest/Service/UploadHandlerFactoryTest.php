<?php

declare(strict_types=1);

namespace ApplicationsTest\Service;

use Applications\Entity\Application;
use Applications\Service\UploadHandler;
use Applications\Service\UploadHandlerFactory;
use Core\Service\FileManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class UploadHandlerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $fileManager = $this->createMock(FileManager::class);
        $dm = $this->createMock(DocumentManager::class);
        $appRepo = $this->createMock(ObjectRepository::class);

        $dm->expects($this->once())
            ->method('getRepository')
            ->with(Application::class)
            ->willReturn($appRepo);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [DocumentManager::class, $dm],
                [FileManager::class, $fileManager],
            ]);

        $handler = (new UploadHandlerFactory())($container);
        $this->assertInstanceOf(UploadHandler::class, $handler);
    }
}
