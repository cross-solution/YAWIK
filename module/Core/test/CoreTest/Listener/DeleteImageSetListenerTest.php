<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Listener;

use Core\Entity\ImageInterface;
use Core\Entity\ImageMetadataInterface;
use Core\Entity\ImageSetInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Core\Entity\Image;
use Core\Listener\DeleteImageSetListener;
use Core\Listener\Events\FileEvent;
use Core\Repository\DefaultRepository;
use Core\Repository\RepositoryService;

/**
 * Tests for \Core\Listener\DeleteImageSetListener
 *
 * @covers \Core\Listener\DeleteImageSetListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class DeleteImageSetListenerTest extends TestCase
{
    /**
     * @var RepositoryService|MockObject
     */
    private $repositories;

    private function getTarget($config)
    {
        $this->repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        return new DeleteImageSetListener($this->repositories, $config);
    }

    public function testReturnsEarlyIfFileIsNotImage()
    {
        $file = $this->createMock(ImageInterface::class);
        $config = [
            Image::class => ['not' => 'empty']
        ];
        $event = new FileEvent();
        $event->setFile($file);

        $target = $this->getTarget($config);
        $this->repositories->expects($this->never())->method('get');

        $this->assertFalse($target($event));
    }

    public function testReturnsEarlyIfNoConfig()
    {
        $image = new Image();
        $event = new FileEvent();
        $event->setFile($image);
        $target = $this->getTarget([]);

        $this->repositories->expects($this->never())->method('get');

        $this->assertFalse($target($event));
    }

    public function testReturnsFalseIfEntityNotFound()
    {
        $image = $this->createMock(ImageInterface::class);
        $metadata = $this->createMock(ImageMetadataInterface::class);

        $image->method('getMetadata')
            ->willReturn($metadata);
        $metadata->method('getBelongsTo')
            ->willReturn('imageSetId');

        $event = new FileEvent();
        $event->setFile($image);

        $config = [
            get_class($image) => [
                'repository' => 'repo',
                'property'   => 'prop',
            ],
        ];

        $target = $this->getTarget($config);

        $repo = $this->getMockBuilder(DefaultRepository::class)->disableOriginalConstructor()->setMethods(['findOneBy'])->getMock();
        $repo->expects($this->once())->method('findOneBy')
            ->with(['prop.id' => 'imageSetId'])
            ->willReturn(null);

        $this->repositories->expects($this->once())
            ->method('get')
            ->with('repo')
            ->willReturn($repo);

        $this->assertFalse($target($event));
    }

    public function testClearsImageSetAndReturnsTrue()
    {
        $image = $this->createMock(ImageInterface::class);
        $metadata = $this->createMock(ImageMetadataInterface::class);

        $image->method('getMetadata')
            ->willReturn($metadata);

        $metadata->method('getBelongsTo')
            ->willReturn('imageSetId');

        $event = new FileEvent();
        $event->setFile($image);

        $config = [
            get_class($image) => [
                'repository' => 'repo',
                'property'   => 'prop',
            ],
        ];

        $target = $this->getTarget($config);
        $imageSet = $this->createMock(ImageSetInterface::class);
        $imageSet->expects($this->once())->method('clear');

        $entity = new Dislt_EntityMock($imageSet);

        $repo = $this->getMockBuilder(DefaultRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy', 'store'])
            ->getMock();
        $repo->expects($this->once())
            ->method('findOneBy')
            ->with(['prop.id' => 'imageSetId'])
            ->willReturn($entity);
        $repo->expects($this->once())
            ->method('store')
            ->with($entity);

        $this->repositories->expects($this->once())
            ->method('get')
            ->with('repo')
            ->willReturn($repo);

        $this->assertTrue($target($event));
    }
}

class Dislt_EntityMock
{
    public function __construct($set)
    {
        $this->set = $set;
    }

    public function getProp()
    {
        return $this->set;
    }
}
