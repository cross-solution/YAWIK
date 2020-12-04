<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests\Migrator;

use Iterator;
use Jean85\Version;
use MongoDB\Client;
use MongoDB\GridFS\Bucket;
use Yawik\Migration\Contracts\ProcessorInterface;
use Yawik\Migration\Exception\MigrationException;
use Yawik\Migration\Migrator\Version36\UserImageProcessor;
use Auth\Service\UploadHandler as AuthHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Database as MongoDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yawik\Migration\Migrator\Version36;
use PHPUnit\Framework\TestCase;
use Yawik\Migration\Tests\TestIterator;
use function MongoDB\with_transaction;

/**
 * Class Version36Test
 *
 * @covers \Yawik\Migration\Migrator\Version36
 * @package Migrator
 */
class Version36Test extends TestCase
{
    /**
     * @var MockObject|OutputInterface
     */
    private $out;

    /**
     * @var AuthHandler|MockObject
     */
    private $handler;

    /**
     * @var DocumentManager|MockObject
     */
    private $dm;

    /**
     * @var Version36
     */
    private Version36 $target;
    /**
     * @var MongoDatabase|MockObject
     */
    private $mongoDB;


    public function setUp(): void
    {
        $this->dm = $this->createMock(DocumentManager::class);
        $this->handler = $this->createMock(AuthHandler::class);
        $this->out = $this->createMock(OutputInterface::class);
        $this->mongoDB = $this->createMock(MongoDatabase::class);
        $container = $this->createMock(ContainerInterface::class);

        $container->method('get')
            ->willReturnMap([
                [DocumentManager::class, $this->dm],
                [AuthHandler::class, $this->handler],
                [OutputInterface::class, $this->out],
            ]);

        $this->dm->method('getDocumentDatabase')
            ->willReturn($this->mongoDB);
        $this->target = new Version36($this->dm, $this->out);
    }

    public function testInfo()
    {
        $target = $this->target;
        $this->assertSame("0.36.0", $target->version());
        $this->assertIsString($target->getDescription());
    }

    public function testSuccesfullyMigrate()
    {
        $target = $this->target;
        $processor = $this->createMock(ProcessorInterface::class);

        $target->addProcessor($processor);
        $processor->expects($this->once())
            ->method('process')
            ->willReturn(true);

        $this->assertTrue($target->migrate());
    }

    public function testOnFailedMigration()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $target = $this->target;

        $target->addProcessor($processor);
        $processor->expects($this->once())
            ->method('process')
            ->willReturn(false);
        $this->assertFalse($target->migrate());
    }

    public function testExecutorThrowsException()
    {
        $processor = $this->createMock(ProcessorInterface::class);
        $target = $this->target;

        $target->addProcessor($processor);
        $processor->expects($this->once())
            ->method('process')
            ->willThrowException(new MigrationException());
        $this->assertFalse($target->migrate());
    }
}