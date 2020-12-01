<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Entity\Hydrator;

use Core\Entity\FileMetadataInterface;
use Core\Entity\ImageInterface;
use Core\Service\FileManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\ImageSetHydrator;
use Core\Entity\Image;
use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use Core\Options\ImageSetOptions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface as ImagineImage;
use Laminas\Hydrator\HydratorInterface;

/**
 * Tests for \Core\Entity\Hydrator\ImageSetHydrator
 *
 * @covers \Core\Entity\Hydrator\ImageSetHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Hydrator
 */
class ImageSetHydratorTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|ImageSetHydrator|MockObject
     */
    private $target = [
        ImageSetHydrator::class,
        'getConstructorArgs',
        '@testConstruction' => false,
        '@testInheritance' => ['as_reflection' => true],
    ];

    private $inheritance = [ HydratorInterface::class ];

    /**
     * @var MockObject|ImagineInterface
     */
    private $imagine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $options;

    /**
     * @var FileManager|MockObject
     */
    private $fileManager;

    private function getConstructorArgs()
    {
        $this->imagine = $this->createMock(ImagineInterface::class);
        $this->options = $this->getMockBuilder(ImageSetOptions::class)
            ->setMethods(['getEntity', 'getImages', 'getFormElementName', 'getMetadata'])
            ->getMock();
        $this->fileManager = $this->createMock(FileManager::class);

        return [$this->fileManager, $this->imagine, $this->options];
    }

    public function testConstruction()
    {
        $this->getConstructorArgs();

        $target = new ImageSetHydrator($this->fileManager, $this->imagine, $this->options);

        $this->assertAttributeSame($this->fileManager, 'fileManager', $target);
        $this->assertAttributeSame($this->imagine, 'imagine', $target);
        $this->assertAttributeSame($this->options, 'options', $target);
    }

    public function testExtractReturnsEmptyArray()
    {
        $this->assertEquals([], $this->target->extract('notCorrectObject'));

        $this->assertEquals([], $this->target->extract(new ImageSet()));
    }

    public function testExtract()
    {
        $this->options->expects($this->once())->method('getFormElementName')->willReturn('name');
        $imageSet = $this->createMock(ImageSetInterface::class);
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(['getId', 'getMetadata'])
            ->getMock();
        $img->expects($this->once())
            ->method('getId')
            ->willReturn('imageId');

        $imageSet->expects($this->once())
            ->method('get')
            ->with(ImageSetInterface::ORIGINAL)
            ->willReturn($img);

        $this->assertEquals(['name' => 'imageId'], $this->target->extract($imageSet));
    }

    public function testHydrationDoesNothingIfNoFileOrUploadError()
    {
        $this->imagine->expects($this->never())->method('open');

        $this->target->hydrate([], new ImageSet());

        $this->target->hydrate(['original' => ['error' => UPLOAD_ERR_NO_FILE]], new ImageSet());
    }

    public function testHydrate()
    {
        $data = [
            'original' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'testimage.png',
                'tmp_name' => 'tmpname',
                'type' => 'image/png',
            ],
        ];

        $imageSpecs = [
            ImageSetInterface::THUMBNAIL => [100,100],
            'large' => [10000,10000],
            'small' => [600,600],
        ];
        $metadata = $this->createMock(FileMetadataInterface::class);
        $image = $this->createMock(ImageInterface::class);
        $fileManager = $this->fileManager;

        $fileManager->expects($this->once())
            ->method('uploadFromFile')
            ->with($metadata, 'tmpname', 'testimage.png')
            ->willReturn($image);

        $fileManager->expects($this->exactly(2))
            ->method('uploadFromStream')
            ->willReturn($image);

        $this->options->expects($this->once())
            ->method('getImages')
            ->willReturn($imageSpecs);

        $this->options->expects($this->exactly(3))
            ->method('getMetadata')
            ->willReturn($metadata);

        $imagineImage = $this->getMockBuilder(ImagineImage::class)
            ->setMethods(['thumbnail', 'getSize', 'resize', 'get'])
            ->getMockForAbstractClass()
        ;
        $imagineImage->expects($this->once())
            ->method('thumbnail')
            ->with($this->equalTo(new Box(100, 100)), ImagineImage::THUMBNAIL_INSET)
            ->will($this->returnSelf());
        $imagineImage->expects($this->exactly(2))
            ->method('getSize')
            ->willReturn(new Box(700, 900));
        $imagineImage->expects($this->exactly(1))
            ->method('resize')
            ->will($this->returnSelf());
        $imagineImage->expects($this->exactly(2))
            ->method('get')
            ->willReturn('filecontentbytes');
        $this->imagine->expects($this->once())
            ->method('open')
            ->with('tmpname')
            ->willReturn($imagineImage);

        $imageSet = $this->createMock(ImageSetInterface::class);
        $imageSet->expects($this->exactly(3))
            ->method('add')
            ->with($this->isInstanceOf(ImageInterface::class));

        $actual = $this->target->hydrate($data, $imageSet);

        $this->assertSame($imageSet, $actual);
    }

}
