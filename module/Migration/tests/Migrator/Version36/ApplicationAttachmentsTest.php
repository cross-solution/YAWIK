<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests\Migrator\Version36;

use Applications\Entity\ApplicationInterface;
use Applications\Service\ApplicationHandler;
use Core\Service\UploadedFileInfo;
use CoreTestUtils\TestCase\FunctionalTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Yawik\Migration\Migrator\Version36\ApplicationAttachments;
use Yawik\Migration\Tests\DatabaseConcernTrait;

/**
 * Class ApplicationAttachmentsExecutorTest
 *
 * @covers \Yawik\Migration\Migrator\Version36\ApplicationAttachments
 * @package Yawik\Migration\Tests\Migrator\Version36
 */
class ApplicationAttachmentsTest extends FunctionalTestCase
{
    use DatabaseConcernTrait;

    /**
     * @var ApplicationAttachments
     */
    private ApplicationAttachments $target;
    /**
     * @var MockObject|OutputInterface
     */
    private $out;

    /**
     * @var ApplicationHandler|MockObject
     */
    private $appHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appHandler = $this->createMock(ApplicationHandler::class);
        $this->out = new StreamOutput(fopen('php://memory', 'w', \false));
        $this->target = new ApplicationAttachments(
            $this->getDoctrine(),
            $this->out,
            $this->appHandler,
        );
        $this->setDbConcernContainer($this->getApplicationServiceLocator());
    }

    private function createTestData()
    {
        $bucket = $this->getBucket('old.applications');
        $this->drop('applications');
        $bucket->drop();


        $fileID = $this->createFile('old.applications');
        $app = $this->loadJson(__DIR__.'/files/application.json');
        $app['images'][] = $fileID;

        $this->insert("applications", $app);
    }

    public function testExecute()
    {
        $target = $this->target;
        $appHandler = $this->appHandler;
        $app = $this->createMock(ApplicationInterface::class);

        $this->createTestData();

        $appHandler->expects($this->once())
            ->method('find')
            ->with('55ae81046b10f87e728b4718')
            ->willReturn($app);
        $appHandler->expects($this->once())
            ->method('handleUpload')
            ->with($this->isInstanceOf(ApplicationInterface::class), $this->isInstanceOf(UploadedFileInfo::class))
            ->willReturn($app)
        ;

        $target->process();
    }
}
