<?php

declare(strict_types=1);

namespace CvTest\Service;

use Applications\Entity\Application;
use Applications\Entity\Attachment as ApplicationAttachment;
use Applications\Entity\Contact as ApplicationContact;
use Auth\Entity\User;
use Auth\Entity\UserInterface;
use Core\Entity\FileInterface;
use Core\Entity\FileMetadataInterface;
use Core\Service\FileManager;
use Cv\Entity\Attachment;
use Cv\Entity\Cv as CvEntity;
use Cv\Repository\Cv as CvRepository;
use Cv\Service\CvHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Jobs\Entity\Job;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CvHandlerTest extends TestCase
{
    /**
     * @var DocumentManager|MockObject
     */
    private $dm;

    /**
     * @var FileManager|MockObject
     */
    private $fileManager;

    /**
     * @var CvRepository|MockObject
     */
    private $cvRepo;

    /**
     * @var CvHandler
     */
    private CvHandler $handler;
    /**
     * @var CvEntity|MockObject
     */
    private $cv;

    public function setUp()
    {
        $this->dm = $this->createMock(DocumentManager::class);
        $this->fileManager = $this->createMock(FileManager::class);
        $this->cv = $this->getMockBuilder(CvEntity::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContact', 'setUser', 'setAttachments'])
            ->getMock();
        $this->cvRepo = $this->getMockBuilder(CvRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->dm->expects($this->any())
            ->method('getRepository')
            ->with(CvEntity::class)
            ->willReturn($this->cvRepo);
        $this->cvRepo->expects($this->any())
            ->method('create')
            ->willReturn($this->cv);

        $this->handler = new CvHandler($this->dm, $this->fileManager);
    }

    public function testCreateCalled()
    {
        $handler = $this->handler;
        $repository = $this->cvRepo;
        $user = $this->createMock(UserInterface::class);
        $cv = $this->cv;
        $job = new Job();

        $repository->expects($this->once())
            ->method('create')
            ->willReturn($cv)
        ;
        $job->setUser($user);


        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['getContact'])
            ->getMock();

        $application->method('getContact')
            ->willReturn(new ApplicationContact());
        $application->setJob($job);

        $cv->expects($this->once())
            ->method('setContact');
        $cv->expects($this->once())
            ->method('setUser')
            ->with($user);

        $cv = $handler->createFromApplication($application, $user);
        $this->assertSame($this->cv, $cv);
    }

    public function testCopyContact()
    {
        $handler = $this->handler;
        $cv = $this->cv;
        $fileManager = $this->fileManager;
        $user = $this->createMock(UserInterface::class);
        // expect proper setting of user to application contact
        $image = $this->createMock(FileInterface::class);
        $metadata = $this->createMock(FileMetadataInterface::class);

        $image->expects($this->any())
            ->method('getMetadata')
            ->willReturn($metadata);

        // expect calling of application contact getImage() method
        $applicationContact = $this->getMockBuilder(ApplicationContact::class)
            ->getMock();

        $job = new Job();
        $job->setUser($user);

        // expect calling of application getContact() method
        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['getContact', 'getAttachments'])
            ->getMock();
        $application->expects($this->once())
            ->method('getContact')
            ->willReturn($applicationContact);
        $application->setJob($job);
        $application->expects($this->once())
            ->method('getAttachments')
            ->willReturn([$image]);

        // expect proper setting of contact to CV
        $cv->expects($this->once())
            ->method('setContact')
            ->with($this->equalTo($applicationContact));

        // attachment copy expectation
        $image->method('getName')
            ->willReturn('name');
        $stream = fopen(__FILE__, 'r');
        $fileManager->expects($this->once())
            ->method('getStream')
            ->with($image)
            ->willReturn($stream);
        $fileManager
            ->expects($this->once())
            ->method('uploadFromStream')
            ->with(
                Attachment::class,
                $this->isInstanceOf(FileMetadataInterface::class),
                'name',
                $stream
            )
            ->willReturn($image);
        $cv->expects($this->once())
            ->method('setAttachments')
            ->with($this->isInstanceOf(ArrayCollection::class));

        $cv = $handler->createFromApplication($application, $user);
        $this->assertSame($this->cv, $cv);
    }
}
