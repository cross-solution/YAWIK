<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener;

use PHPUnit\Framework\TestCase;

use Core\Entity\FileEntity;
use Core\Entity\Image;
use Core\Entity\ImageSet;
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
    private function getTarget($config)
    {
        $this->repositories = $this->getMockBuilder(RepositoryService::class)->disableOriginalConstructor()
            ->setMethods(['get'])->getMock();

        $target = new DeleteImageSetListener($this->repositories, $config);

        return $target;
    }

    public function testReturnsEarlyIfFileIsNotImage()
    {
        $file = new FileEntity();
        $config = [
            FileEntity::class => ['not' => 'empty']
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
        $image = new Image;
        $image->setBelongsTo('imageSetId');

        $event = new FileEvent();
        $event->setFile($image);

        $config = [
            Image::class => [
                'repository' => 'repo',
                'property'   => 'prop',
            ],
        ];

        $target = $this->getTarget($config);

        $repo = $this->getMockBuilder(DefaultRepository::class)->disableOriginalConstructor()->setMethods(['findOneBy'])->getMock();
        $repo->expects($this->once())->method('findOneBy')->with(['prop.id' => 'imageSetId'])->willReturn(null);

        $this->repositories->expects($this->once())->method('get')->with('repo')->willReturn($repo);

        $this->assertFalse($target($event));
    }

    public function testClearsImageSetAndReturnsTrue()
    {
        $image = new Image;
        $image->setBelongsTo('imageSetId');

        $event = new FileEvent();
        $event->setFile($image);

        $config = [
            Image::class => [
                'repository' => 'repo',
                'property'   => 'prop',
            ],
        ];

        $target = $this->getTarget($config);
        $imageSet = $this->getMockBuilder(ImageSet::class)->setMethods(['clear'])->getMock();
        $imageSet->expects($this->once())->method('clear');

        $entity = new Dislt_EntityMock($imageSet);

        $repo = $this->getMockBuilder(DefaultRepository::class)->disableOriginalConstructor()->setMethods(['findOneBy'])->getMock();
        $repo->expects($this->once())->method('findOneBy')->with(['prop.id' => 'imageSetId'])->willReturn($entity);

        $this->repositories->expects($this->once())->method('get')->with('repo')->willReturn($repo);

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
