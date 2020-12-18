<?php

declare(strict_types=1);

namespace ApplicationsTest\Service;


use Applications\Entity\ApplicationInterface;
use Applications\Entity\Attachment;
use Applications\Entity\Contact;
use Applications\Service\UploadHandler;
use Auth\Entity\UserInterface;
use Core\Entity\FileMetadata;
use Core\Service\FileManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class UploadHandlerTest extends TestCase
{
    private UploadHandler $target;
    /**
     * @var DocumentManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dm;
    /**
     * @var FileManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fileManager;
    /**
     * @var ObjectRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $appRepo;

    public function setUp()
    {
        $this->dm = $this->createMock(DocumentManager::class);
        $this->fileManager = $this->createMock(FileManager::class);
        $this->appRepo = $this->createMock(ObjectRepository::class);

        $this->target = new UploadHandler(
            $this->dm,
            $this->fileManager,
            $this->appRepo
        );
    }

    public function testFindApplication()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $this->appRepo
            ->expects($this->once())
            ->method('find')
            ->with('id')
            ->willReturn($app);

        $result = $this->target->findApplication('id');

        $this->assertSame($app, $result);
    }

    public function testHandleAttachmentUpload()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $user = $this->createMock(UserInterface::class);
        $attachments = $this->createMock(Collection::class);
        $attachment = $this->createMock(Attachment::class);

        $info = [
            'type' => 'pdf',
            'tmp_name' => '/tmp/foo',
            'name' => 'foo.pdf'
        ];

        $app->method('getUser')->willReturn($user);
        $app->method('getAttachments')->willReturn($attachments);

        $this->appRepo->method('find')
            ->with('id')
            ->willReturn($app);

        $this->dm->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive(
                [$user],
                [$app]
            );
        $this->dm->expects($this->once())->method('flush');

        $this->fileManager->expects($this->once())
            ->method('uploadFromFile')
            ->with(
                Attachment::class,
                $this->isInstanceOf(FileMetadata::class),
                '/tmp/foo',
                'foo.pdf'
            )
            ->willReturn($attachment)
        ;
        $attachments->expects($this->once())
            ->method('add')
            ->with($attachment);

        $result = $this->target->handleAttachmentUpload('id', $info);
        $this->assertSame($attachment, $result);
    }

    public function testHandleImageUpload()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $user = $this->createMock(UserInterface::class);
        $image = $this->createMock(Attachment::class);
        $contact = $this->createMock(Contact::class);
        $info = [
            'type' => 'pdf',
            'tmp_name' => '/tmp/foo',
            'name' => 'foo.pdf'
        ];

        $contact->method('getImage')->willReturn($image);
        $app->method('getUser')->willReturn($user);
        $app->method('getContact')->willReturn($contact);

        $this->appRepo->method('find')
            ->with('id')
            ->willReturn($app);

        $this->dm->expects($this->exactly(3))
            ->method('persist')
            ->withConsecutive(
                [$user],
                [$app],
                [$app]
            );
        $this->dm->expects($this->once())
            ->method('remove')
            ->with($image);

        $this->dm->expects($this->exactly(2))->method('flush');

        $this->fileManager->expects($this->once())
            ->method('uploadFromFile')
            ->with(
                Attachment::class,
                $this->isInstanceOf(FileMetadata::class),
                '/tmp/foo',
                'foo.pdf'
            )
            ->willReturn($image)
        ;

        $contact->expects($this->exactly(2))
            ->method('setImage')
            ->withConsecutive([null],[$image]);
        $result = $this->target->handleImageUpload('id', $info);

        $this->assertSame($app, $result);
    }
}